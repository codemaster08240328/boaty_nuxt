'use strict'

const { Command } = require('@adonisjs/ace')

const Database = use('Database')

const MmkBoat = use('App/Models/MmkBoat')
const MmkBoatKind = use('App/Models/MmkBoatKind')
const MmkBoatType = use('App/Models/MmkBoatType')
const MmkBoatGenericType = use('App/Models/MmkBoatGenericType')
const MmkBoatServiceType = use('App/Models/MmkBoatServiceType')
const MmkBase = use('App/Models/MmkBase')
const MmkCity = use('App/Models/MmkCity')
const MmkRegion = use('App/Models/MmkRegion')
const MmkSaturdayService = use('App/Services/MmkSaturdayService')

class FillMissingMmkData extends Command {
  static get signature () {
    return 'mmk:update-missing'
  }

  static get description () {
    return 'Fill database with missing data (DEVELOPER ONLY)'
  }

  async handle (args, options) {
    // throw new Error('Do not run this when not needed')

    // console.log('city data...')
    // await this.createCities()
    //
    // console.log('Region slugs...')
    // await this.fillRegionSlugs()
    //
    // console.log('Base slugs...')
    // await this.fillBaseSlugs()
    //
    // console.log('Boat relations...')
    // await this.fillBoatData()

    await this.fillSaturdayData()
    Database.close()
  }

  async fillSaturdayData () {
    let ids = await MmkBoat.ids()

    let total = ids.length
    for (let i = 0; i < total; i++) {
      let boat = await MmkBoat.find(ids[i])
      let service = new MmkSaturdayService(boat)
      await service.fillDetails()
      console.log('Boat ' + boat.name, ', price ' + boat.saturday_price + ', base ' + boat.saturday_base_id)
    }
  }

  async fillBoatData () {
    let types = await MmkBoatType.all()
    types = types.rows

    let genericTypes = await MmkBoatGenericType.all()
    genericTypes = genericTypes.rows

    let kinds = await MmkBoatKind.all()
    kinds = kinds.rows

    let serviceTypes = await MmkBoatServiceType.all()
    serviceTypes = serviceTypes.rows

    let boats = await MmkBoat.all()
    boats = boats.rows

    let total = boats.length
    for (let i = 0; i < total; i++) {
      let boat = boats[i]

      if (boat.model_id === null) {
        let type = types.find(function (item) {
          return item.name === boat.model
        })

        if (typeof type === 'undefined') {
          type = new MmkBoatType()
          type.name = boat.model
          await type.save()
          types.push(type)
        }

        boat.model_id = type.id
      }

      if (boat.kind_id === null) {
        let kind = kinds.find(function (item) {
          return item.name === boat.kind
        })

        if (typeof kind === 'undefined') {
          kind = new MmkBoatKind()
          kind.name = boat.kind
          await kind.save()
          kinds.push(kind)
        }

        boat.kind_id = kind.id
      }

      if (boat.service_type_id === null) {
        let type = serviceTypes.find(function (item) {
          return item.name === boat.service_type
        })

        if (typeof type === 'undefined') {
          type = new MmkBoatServiceType()
          type.name = boat.service_type
          await type.save()
          serviceTypes.push(type)
        }

        boat.service_type_id = type.id
      }

      if (boat.generic_type_id === null) {
        let type = genericTypes.find(function (item) {
          return item.name === boat.generic_type_name
        })

        if (typeof type === 'undefined') {
          type = new MmkBoatGenericType()
          type.name = boat.generic_type_name
          await type.save()
          genericTypes.push(type)
        }

        boat.generic_type_id = type.id
      }

      await boat.save()
    }
  }

  async fillRegionSlugs () {
    let regions = await MmkRegion.all()
    regions = regions.rows

    let total = regions.length
    for (let i = 0; i < total; i++) {
      let region = regions[i]
      let name = region.name
      region.name = '---'
      await region.save()

      region.name = name
      await region.save()
    }
  }

  async fillBaseSlugs () {
    let bases = await MmkBase.all()
    bases = bases.rows

    let total = bases.length
    for (let i = 0; i < total; i++) {
      let base = bases[i]
      let name = base.name
      base.name = '---'
      await base.save()

      base.name = name
      await base.save()
    }
  }

  async createCities () {
    let cities = await MmkCity.all()
    cities = cities.rows

    let bases = await MmkBase.query()
      .where('city_id', null)
      .fetch()
    bases = bases.rows

    let total = bases.length
    for (let i = 0; i < total; i++) {
      let base = bases[i]
      let city = cities.find(function (item) {
        return item.name === base.city && item.country_id === base.country_id
      })

      if (typeof city === 'undefined') {
        city = new MmkCity()
        city.name = base.city
        city.country_id = base.country_id
        await city.save()
        cities.push(city)
      }

      base.city_id = city.id
      await base.save()
    }
  }
}

module.exports = FillMissingMmkData
