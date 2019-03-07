'use strict'

const Country = use('App/Models/Country')
const Area = use('App/Models/Area')
const Base = use('App/Models/Base')
const Boat = use('App/Models/Boat')
const BoatBrand = use('App/Models/BoatBrand')
const BoatModel = use('App/Models/BoatModel')
const BoatType = use('App/Models/BoatType')
const Database = use('Database')
const Cabin = use('App/Models/Cabin')
const FleetOperator = use('App/Models/FleetOperator')
const Url = use('App/Services/Url')

class SearchParamsController {
  async regions ({ response }) {
    let data = await Database.from('v1_regions_with_boats')
      .select('*')
      .orderBy('popular', 'desc')
      .orderBy('country_name_en', 'desc')
      .orderBy('area_name_en', 'desc')
      .orderBy('base_name', 'desc')

    let searchRegions = []
    let lastCountry = ''
    let lastArea = ''

    for (let region of data) {
      let searchRegion = false
      if (lastCountry !== region.country_name_en) {
        lastCountry = region.country_name_en
        // add a header first
        // searchRegion = {
        //   header: region.country_name_en
        // }
        // searchRegions.push(searchRegion)
        searchRegion = {
          ID: region.country_id,
          Name: region.country_name_en,
          Icon: 'globe',
          Type: 'country',
          Url: Url.stringToUrl({ urlParts: [region.country_name_en] })
        }

        searchRegions.push(searchRegion)
      }

      if (lastArea !== region.area_name_en) {
        lastArea = region.area_name_en

        searchRegion = {
          ID: region.area_id,
          Name: region.area_name_en + ', ' + region.country_name_en,
          Icon: 'flag',
          Type: 'area',
          Url: Url.stringToUrl({ urlParts: [region.country_name_en, region.area_name_en] })
        }

        searchRegions.push(searchRegion)
      }

      if (region.base_id != null) {
        searchRegion = {
          ID: region.base_id,
          Name: region.base_name + ', ' + region.area_name_en + ', ' + region.country_name_en,
          Icon: 'ship',
          Type: 'base',
          Url: Url.stringToUrl({ urlParts: [region.country_name_en, region.area_name_en, region.base_name] })
        }
      }

      if (searchRegion) {
        searchRegions.push(searchRegion)
      }
    }

    response.send(searchRegions)
  }

  async regionsRaw () {
    return Database.select('*').from('regions')
  }

  async countries ({ response }) {
    const countries = await Country.all()
    response.send(countries)
  }

  async areas ({ response }) {
    const areas = await Area.all()
    response.send(areas)
  }

  async bases ({ response }) {
    const bases = await Base.all()
    response.send(bases)
  }

  async boats ({ response }) {
    const boats = await Boat.all()
    response.send(boats)
  }

  async boatbrands ({ response }) {
    const boatbrands = await BoatBrand.all()
    response.send(boatbrands)
  }

  async boatmodels ({ response }) {
    const boatmodels = await BoatModel.all()
    response.send(boatmodels)
  }

  async boattypes ({ response }) {
    const boattypes = await BoatType.all()
    response.send(boattypes)
  }

  async cabins ({ response }) {
    const cabins = await Cabin.all()
    response.send(cabins)
  }

  async fleetoperators ({ response }) {
    const fleetoperators = await FleetOperator.all()
    response.send(fleetoperators)
  }
}

module.exports = SearchParamsController
