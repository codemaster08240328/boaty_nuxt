'use strict'

const bigXml = use('App/Services/big-xml')

const { Command } = require('@adonisjs/ace')
const Database = use('Database')

const MmkBase = use('App/Models/MmkBase')
const MmkBoat = use('App/Models/MmkBoat')
const MmkBoatCategory = use('App/Models/MmkBoatCategory')
const MmkBoatDiscount = use('App/Models/MmkBoatDiscount')
const MmkBoatEquipment = use('App/Models/MmkBoatEquipment')
const MmkBoatExtra = use('App/Models/MmkBoatExtra')
const MmkBoatGenericType = use('App/Models/MmkBoatGenericType')
const MmkBoatImage = use('App/Models/MmkBoatImage')
const MmkBoatKind = use('App/Models/MmkBoatKind')
const MmkBoatLocation = use('App/Models/MmkBoatLocation')
const MmkBoatPrice = use('App/Models/MmkBoatPrice')
const MmkBoatProduct = use('App/Models/MmkBoatProduct')
const MmkBoatServiceType = use('App/Models/MmkBoatServiceType')
const MmkBoatType = use('App/Models/MmkBoatType')
const MmkCity = use('App/Models/MmkCity')
const MmkCompany = use('App/Models/MmkCompany')
const MmkCountry = use('App/Models/MmkCountry')
const MmkEquipmentCategory = use('App/Models/MmkEquipmentCategory')
const MmkProductDiscount = use('App/Models/MmkProductDiscount')
const MmkProductExtra = use('App/Models/MmkProductExtra')
const MmkProductPrice = use('App/Models/MmkProductPrice')
const MmkRegion = use('App/Models/MmkRegion')
const apiClient = use('App/Services/MMKApiClient')
const MmkCounterService = use('App/Services/MmkCounterService')

const fs = require('fs')

class SyncMmk extends Command {
  static get signature () {
    return 'mmk:sync'
  }

  static get description () {
    return 'Sync database content with MMK API'
  }

  async handle (args, options) {
    console.log('Removing all expired data...')
    await this.removeExpiredData()

    console.log('Preloading required data...')
    await this.preloadData()

    console.log('Syncing regions...')
    await this.syncRegions()

    console.log('Syncing countries...')
    await this.syncCountries()

    console.log('Syncing bases...')
    await this.syncBases()

    console.log('Syncing equipment categories...')
    await this.syncEquipmentCategories()

    console.log('Syncing companies and their resources')
    await this.syncCompanies()

    console.log('Generating statistics...')
    let countService = new MmkCounterService()
    await countService.updateCounters()
  }

  async removeExpiredData () {
    let today = new Date()

    console.log('Boat discounts')
    await MmkBoatDiscount.query()
      .where('valid_to', '<', today)
      .delete()

    console.log('Boat extras')
    await MmkBoatExtra.query()
      .where('valid_to', '<', today)
      .delete()

    console.log('Boat prices')
    await MmkBoatPrice.query()
      .where('date_to', '<', today)
      .delete()

    console.log('Product discounts')
    await MmkProductDiscount.query()
      .where('valid_to', '<', today)
      .delete()

    console.log('Product extras')
    await MmkProductExtra.query()
      .where('valid_to', '<', today)
      .delete()

    console.log('Product prices')
    await MmkProductPrice.query()
      .where('date_to', '<', today)
      .delete()
  }

  async preloadData () {
    let result = await MmkCity.all()
    SyncMmk.cities = result.rows

    result = await MmkBoatType.all()
    SyncMmk.boatTypes = result.rows

    result = await MmkBoatGenericType.all()
    SyncMmk.boatGenericTypes = result.rows

    result = await MmkBoatServiceType.all()
    SyncMmk.boatServiceTypes = result.rows

    result = await MmkBoatKind.all()
    SyncMmk.boatKinds = result.rows
  }

  /* Sync MMK regions */
  async syncRegions () {
    return new Promise(async function (resolve, reject) {
      let dbRegions = await MmkRegion.all()
      dbRegions = dbRegions.rows

      let cacheFilename = '/tmp/regions.xml'
      let regionsXml = await apiClient.getRegions()
      if (regionsXml === null) {
        return resolve()
      }

      fs.writeFileSync(cacheFilename, regionsXml, 'utf8')

      let promises = []

      let reader = bigXml.createReader(cacheFilename, /^region$/)
      reader.on('error', function (error) {
        reject(error)
      })

      reader.on('close', async function () {
        await Promise.all(promises)
        Database.close()
        resolve()
      })

      reader.on('record', function (region) {
        promises.push(new Promise(async function (resolve1, reject1) {
          let mmkId = SyncMmk.stringToInt(region.attrs.id)
          let name = region.attrs.name

          let dbRegion = dbRegions.find(function (item) {
            return item.mmk_id === mmkId
          })

          if (typeof dbRegion === 'undefined') {
            console.log('Storing new region ' + name + ' in database')
            dbRegion = new MmkRegion()
            dbRegion.mmk_id = mmkId
            dbRegion.name = name
            await dbRegion.save()
          }
          resolve1()
        }))
      })
    })
  }

  async syncCountries () {
    return new Promise(async function (resolve, reject) {
      let cacheFilename = '/tmp/countries.xml'
      let countriesXml = await apiClient.getCountries()
      if (countriesXml === null) {
        return resolve()
      }

      fs.writeFileSync(cacheFilename, countriesXml, 'utf8')

      let promises = []

      let reader = bigXml.createReader(cacheFilename, /^country$/)
      reader.on('error', function (error) {
        reject(error)
      })

      reader.on('close', async function () {
        let countries = await Promise.all(promises)

        let total = countries.length
        for(let i = 0; i < total; i++) {
          await SyncMmk.syncSingleCountry(countries[i])
        }

        Database.close()
        resolve()
      })

      reader.on('record', function (country) {
        promises.push(new Promise(async function (resolve1, reject1) {
          resolve1(country)
        }))
      })
    })
  }

  static async syncSingleCountry (country) {
    return new Promise(async function(resolve, reject) {
      let mmkId = SyncMmk.stringToInt(country.attrs.id)

      // If country is present in database, skip it
      let dbCountry = await MmkCountry.findBy('mmk_id', mmkId)

      if (dbCountry !== null) {
        resolve()
        return
      }

      let mmkRegionId = SyncMmk.stringToInt(country.attrs.regionid)
      let dbRegion = await MmkRegion.findBy('mmk_id', mmkRegionId)


      if (typeof dbRegion === 'undefined') {
        reject1('Could not find region in database for country with MMK ID ' + mmkId)
        return
      }

      // Store new country in database
      dbCountry = new MmkCountry()
      dbCountry.mmk_id = mmkId
      dbCountry.region_id = dbRegion.id
      dbCountry.name = country.attrs.name
      dbCountry.alpha2 = country.attrs.shortname
      dbCountry.alpha3 = country.attrs.longshortname

      console.log('Saving new country in database: ' + dbCountry.name)
      await dbCountry.save()

      resolve()
    })
  }

  async syncBases () {
    return new Promise(async function (resolve, reject) {
      let cacheFilename = '/tmp/bases.xml'
      let basesXml = await apiClient.getBases()
      if (basesXml === null) {
        return resolve()
      }

      fs.writeFileSync(cacheFilename, basesXml, 'utf8', function() {})

      let promises = []

      let reader = bigXml.createReader(cacheFilename, /^base$/)
      reader.on('error', function (error) {
        reject(error)
      })

      reader.on('close', async function () {
        let bases = await Promise.all(promises)

        let total = bases.length
        for(let i = 0; i < total; i++) {
          await SyncMmk.syncSingleBase(bases[i])
        }

        Database.close()
        resolve()
      })

      reader.on('record', function (country) {
        promises.push(new Promise(async function (resolve1, reject1) {
          resolve1(country)
        }))
      })
    })
  }

  static async syncSingleBase (base) {
    return new Promise(async function (resolve, reject) {
      let mmkId = SyncMmk.stringToInt(base.attrs.id)

      // If base is present in database, skip it
      let dbBase = await MmkBase.findBy('mmk_id', mmkId)

      if (dbBase !== null) {
        resolve()
        return
      }

      dbBase = new MmkBase()
      dbBase.mmk_id = mmkId
      dbBase.name = base.attrs.name
      dbBase.city = base.attrs.city
      dbBase.country_alpha2 = base.attrs.country
      dbBase.address = base.attrs.address
      dbBase.longitude = SyncMmk.stringToFloat(base.attrs.longitude)
      dbBase.latitude = SyncMmk.stringToFloat(base.attrs.latitude)

      // Some records are having bad values
      if (Math.abs(dbBase.longitude) > 360.0 || isNaN(dbBase.longitude)) {
        console.error('Wrong longitude for base ' + dbBase.name)
        dbBase.longitude = null
      }

      if (Math.abs(dbBase.latitude) > 360.0 || isNaN(dbBase.latitude)) {
        console.error('Wrong latitude for base ' + dbBase.name)
        dbBase.latitude = null
      }

      // Country relation
      let mmkCountryId = SyncMmk.stringToInt(base.attrs.countryid)
      let dbCountry = await MmkCountry.findBy('mmk_id', mmkCountryId)

      if (typeof dbCountry === 'undefined') {
        reject('Could not find country in database with MMK ID ' + mmkCountryId)
        return
      }

      dbBase.country_id = dbCountry.id

      let city = SyncMmk.cities.find(function (item) {
        return item.name === dbBase.city && item.country_id === dbBase.country_id
      })

      if (typeof city === 'undefined') {
        city = new MmkCity()
        city.name = dbBase.city
        city.country_id = dbBase.country_id
        await city.save()
        SyncMmk.cities.push(city)
      }

      dbBase.city_id = city.id

      console.log('Saving new base in database ' + dbBase.name)

      await dbBase.save()

      // Regions relation
      let regionIds = base.attrs.regionids.split(',')
      let totalRegions = regionIds.length
      for (let j = 0; j < totalRegions; j++) {
        let dbRegion = await MmkRegion.findBy('mmk_id', SyncMmk.stringToInt(regionIds[j]))

        if (typeof dbRegion === 'undefined') {
          reject('Could not find region in database with MMK ID ' + regionIds[j])
          return
        }

        await dbBase.regions().attach([dbRegion.id])
      }
      resolve()
    })
  }

  async syncEquipmentCategories () {
    return new Promise(async function (resolve, reject) {
      let cacheFilename = '/tmp/equipment_categories.xml'
      let categoriesXml = await apiClient.getEquipmentCategories()
      if (categoriesXml === null) {
        return resolve()
      }

      fs.writeFileSync(cacheFilename, categoriesXml, 'utf8', function() {})

      let promises = []

      let reader = bigXml.createReader(cacheFilename, /^equipmentcategory$/)
      reader.on('error', function (error) {
        reject(error)
      })

      reader.on('close', async function () {
        let categories = await Promise.all(promises)

        let total = categories.length
        for(let i = 0; i < total; i++) {
          await SyncMmk.syncSingleEquipmentCategory(categories[i])
        }

        Database.close()
        resolve()
      })

      reader.on('record', function (category) {
        promises.push(new Promise(async function (resolve1, reject1) {
          resolve1(category)
        }))
      })
    })
  }

  static async syncSingleEquipmentCategory (category) {
    return new Promise(async function (resolve, reject) {
      let mmkId = SyncMmk.stringToInt(category.attrs.id)

      // If category is present, skip it
      let dbCategory = await MmkEquipmentCategory.findBy('mmk_id', mmkId)

      if (dbCategory !== null) {
        resolve()
        return
      }

      dbCategory = new MmkEquipmentCategory()
      dbCategory.mmk_id = mmkId
      dbCategory.name = category.attrs.name

      console.log('Adding new equipment category ' + dbCategory.name)
      await dbCategory.save()
      resolve()
    })
  }

  async syncCompanies () {
    return new Promise(async function (resolve, reject) {
      let cacheFilename = '/tmp/companies.xml'
      let companiesXml = await apiClient.getCompanies()
      if (companiesXml === null) {
        return resolve()
      }

      fs.writeFileSync(cacheFilename, companiesXml, 'utf8', function() {})

      let promises = []

      let reader = bigXml.createReader(cacheFilename, /^company$/)
      reader.on('error', function (error) {
        reject(error)
      })

      reader.on('close', async function () {
        let companies = await Promise.all(promises)

        let total = companies.length
        for(let i = 0; i < total; i++) {
          await SyncMmk.syncSingleCompany(companies[i])
        }

        Database.close()
        resolve()
      })

      reader.on('record', function (company) {
        promises.push(new Promise(async function (resolve1, reject1) {
          resolve1(company)
        }))
      })
    })
  }

  static async syncSingleCompany (company) {
    return new Promise(async function (resolve, reject) {
      let mmkId = SyncMmk.stringToInt(company.attrs.id)

      let dbCompany = await MmkCompany.findBy('mmk_id', mmkId)

      if (dbCompany === null) {
        dbCompany = new MmkCompany()
        dbCompany.mmk_id = mmkId
      }

      dbCompany.name = company.attrs.name
      dbCompany.address = company.attrs.address
      dbCompany.city = company.attrs.city
      dbCompany.zip = company.attrs.zip
      dbCompany.country_name = company.attrs.country
      dbCompany.phone = company.attrs.telephone1
      dbCompany.phone2 = company.attrs.telephone2
      dbCompany.fax = company.attrs.fax1
      dbCompany.fax2 = company.attrs.fax2
      dbCompany.mobile = company.attrs.mobile1
      dbCompany.mobile2 = company.attrs.mobile2
      dbCompany.vat_code = company.attrs.vatcode
      dbCompany.email = company.attrs.email
      dbCompany.web = company.attrs.web
      dbCompany.availability = SyncMmk.stringToInt(company.attrs.availability)

      console.log('Updating company deatils: ' + dbCompany.name)
      await dbCompany.save()
      await SyncMmk.syncBoats(dbCompany)

      resolve(dbCompany)
    })
  }

  static async syncBoats (company) {
    return new Promise(async function (resolve, reject) {
      let cacheFilename = '/tmp/resources.xml'
      let resourcesXml = await apiClient.getResources(company.mmk_id)
      if (resourcesXml === null) {
        return resolve()
      }

      fs.writeFileSync(cacheFilename, resourcesXml, 'utf8', function() {})

      let promises = []
      let existingIds = await company.boats().ids()

      let reader = bigXml.createReader(cacheFilename, /^resource$/)
      reader.on('error', function (error) {
        reject(error)
      })

      reader.on('close', async function () {
        let resources = await Promise.all(promises)

        let total = resources.length
        let processedIds = []
        for(let i = 0; i < total; i++) {
          let id = await SyncMmk.syncSingleBoat(company, resources[i])
          processedIds.push(id)
        }

        let idsToRemove = existingIds.filter(id => !processedIds.includes(id))
        total = idsToRemove.length
        console.log('Number of boats to remove from database: ' + total)

        for(let i = 0; i < total; i++) {
          let boat = await MmkBoat.find(idsToRemove[i])
          await SyncMmk.deleteBoat(boat)
        }

        resolve()
      })

      reader.on('record', function (resource) {
        promises.push(new Promise(async function (resolve1, reject1) {
          resolve1(resource)
        }))
      })
    })
  }

  static async syncSingleBoat (company, boat) {
    return new Promise(async function(resolve, reject) {
      let mmkId = SyncMmk.stringToInt(boat.attrs.id)

      let dbBoat = await MmkBoat.findBy('mmk_id', mmkId)

      if (dbBoat === null) {
        dbBoat = new MmkBoat()
        dbBoat.mmk_id = mmkId
      }

      // Base - if it is not present, ignore the boat
      let baseId = SyncMmk.stringToInt(boat.attrs.base)
      let dbBase = await MmkBase.findBy('mmk_id', baseId)

      if (dbBase === null) {
        console.error('Could not find base with ID ' + baseId)
        resolve(null)
        return
      }

      dbBoat.company_id = company.id
      dbBoat.mmk_gid = SyncMmk.stringToInt(boat.attrs.gid)
      dbBoat.name = boat.attrs.name
      dbBoat.model = boat.attrs.model
      dbBoat.berths = SyncMmk.stringToInt(boat.attrs.berths)
      dbBoat.year = SyncMmk.stringToInt(boat.attrs.year)
      dbBoat.length = SyncMmk.stringToFloat(boat.attrs.length)
      dbBoat.cabins = SyncMmk.stringToInt(boat.attrs.cabins)
      dbBoat.heads = SyncMmk.stringToInt(boat.attrs.heads)
      dbBoat.water_capacity = boat.attrs.watercapacity
      dbBoat.fuel_capacity = boat.attrs.fuelcapacity
      dbBoat.engine = boat.attrs.engine
      dbBoat.deposit = SyncMmk.stringToFloat(boat.attrs.deposit)
      dbBoat.deposit_with_waiver = SyncMmk.stringToFloat(boat.attrs.depositwithwaiver)
      dbBoat.commission = SyncMmk.stringToFloat(boat.attrs.commissionpercentage)
      dbBoat.draught = boat.attrs.draught
      dbBoat.beam = boat.attrs.beam
      dbBoat.length_at_waternline = boat.attrs.lengthatwaterline
      dbBoat.kind = boat.attrs.kind
      dbBoat.max_discount = SyncMmk.stringToFloat(boat.attrs.maximumdiscount)
      dbBoat.service_type = boat.attrs.servicetype
      dbBoat.discounts_have_subtotals = boat.attrs.discountshavesubtotals === '1'
      dbBoat.checkin_time = boat.attrs.defaultcheckintime
      dbBoat.checkout_time = boat.attrs.defaultcheckouttime
      dbBoat.discount_without_vat = boat.attrs.calculateagencydiscountwithoutvat
      dbBoat.taxable_amount = SyncMmk.stringToFloat(boat.attrs.taxableamount)
      dbBoat.tax_rate = SyncMmk.stringToFloat(boat.attrs.taxrate)
      dbBoat.generic_type_name = boat.attrs.genericresourcetypename
      dbBoat.checkin_day = SyncMmk.stringToInt(boat.attrs.defaultcheckinday)
      dbBoat.user_code_id = boat.attrs.codeid
      dbBoat.transit_log = SyncMmk.stringToInt(boat.attrs.transitlog)
      dbBoat.cleaning_cost = SyncMmk.stringToFloat(boat.attrs.defaultcleaningcost)
      dbBoat.shipyard_id = boat.attrs.shipyardid
      dbBoat.sale_price = SyncMmk.stringToFloat(boat.attrs.saleprice)
      dbBoat.license_required = boat.attrs.requiredskipperlicense === '1'

      let ownerId = SyncMmk.stringToInt(boat.attrs.ownerid)
      if (ownerId !== -1) {
        dbBoat.owner_id = ownerId
      } else {
        dbBoat.owner_id = null
      }

      dbBoat.mainsail = boat.attrs.mainsail
      dbBoat.genoa = boat.attrs.genoa

      console.log('Syncing boat ' + dbBoat.name + ' (ID: ' + dbBoat.mmk_id + ')')

      await SyncMmk.syncBoatRelations(dbBoat)

      dbBoat.base_id = dbBase.id
      await dbBoat.save()

      // We need to setup default base in here as we may not have custom locations
      let defaultBase = await dbBoat.locations().defaultBase().first()
      if (defaultBase === null) {
        defaultBase = new MmkBoatLocation()
        defaultBase.boat_id = dbBoat.id
        defaultBase.base_id = dbBoat.base_id
        defaultBase.default_base = true

        await defaultBase.save()
      } else if (defaultBase.base_id !== dbBoat.base_id) {
        defaultBase.base_id = dbBoat.base_id
        await defaultBase.save()
      }

      // Go through subitems like prices etc.
      let childTotal = boat.children.length
      for (let i = 0; i < childTotal; i++) {
        let child = boat.children[i]

        if(typeof child.children === 'undefined') {
          continue
        }

        switch (child.tag) {
          case 'prices':
            await SyncMmk.syncBoatPrices(dbBoat, child.children)
            break
          case 'images':
            await SyncMmk.syncBoatImages(dbBoat, child.children)
            break
          case 'equipment':
            await SyncMmk.syncBoatEquipments(dbBoat, child.children)
            break
          case 'extras':
            await SyncMmk.syncBoatExtras(dbBoat, child.children)
            break
          case 'discounts':
            await SyncMmk.syncBoatDiscounts(dbBoat, child.children)
            break
          case 'locations':
            await SyncMmk.syncBoatLocations(dbBoat, child.children)
            break
          case 'categories':
            await SyncMmk.syncBoatCategories(dbBoat, child.children)
            break
          case 'products':
            await SyncMmk.syncBoatProducts(dbBoat, child.children)
            break
          default:
            console.log('Ignoring boat child node ' + child.tag)
        }
      }

      // If boat has no active prices, then it will be scheduled to be deleted
      let prices = await dbBoat.prices().count()
      if (prices === 0) {
        console.log('No price data has been found, this boat will be removed from database')
        resolve(null)
      } else {
        resolve(dbBoat.id)
      }
    })
  }

  static async syncBoatRelations (boat) {
    let type = SyncMmk.boatTypes.find(function (item) {
      return item.name === boat.model
    })

    if (typeof type === 'undefined') {
      type = new MmkBoatType()
      type.name = boat.model
      await type.save()
      SyncMmk.boatTypes.push(type)
    }

    boat.model_id = type.id

    let kind = SyncMmk.boatKinds.find(function (item) {
      return item.name === boat.kind
    })

    if (typeof kind === 'undefined') {
      kind = new MmkBoatKind()
      kind.name = boat.kind
      await kind.save()
      SyncMmk.boatKinds.push(kind)
    }

    boat.kind_id = kind.id

    type = SyncMmk.boatServiceTypes.find(function (item) {
      return item.name === boat.service_type
    })

    if (typeof type === 'undefined') {
      type = new MmkBoatServiceType()
      type.name = boat.service_type
      await type.save()
      SyncMmk.boatServiceTypes.push(type)
    }

    boat.service_type_id = type.id

    type = SyncMmk.boatGenericTypes.find(function (item) {
      return item.name === boat.generic_type_name
    })

    if (typeof type === 'undefined') {
      type = new MmkBoatGenericType()
      type.name = boat.generic_type_name
      await type.save()
      SyncMmk.boatGenericTypes.push(type)
    }

    boat.generic_type_id = type.id
  }

  static async syncBoatProducts (boat, products) {
    let processedIds = []
    let dbProducts = await boat.products().fetch()
    dbProducts = dbProducts.rows

    let total = products.length
    for (let i = 0; i < total; i++) {
      let product = products[i]

      let name = product.attrs.name
      let dbProduct = dbProducts.find(function (item) {
        return item.name === name
      })

      if (typeof dbProduct === 'undefined') {
        dbProduct = new MmkBoatProduct()
        dbProduct.boat_id = boat.id
      } else {
        processedIds.push(dbProduct.id)
      }

      dbProduct.name = name
      await dbProduct.save()

      // Go through subitems like prices etc.
      let childTotal = product.children.length
      for (let i = 0; i < childTotal; i++) {
        let child = product.children[i]

        if(typeof child.children === 'undefined') {
          continue
        }

        switch (child.tag) {
          case 'prices':
            await SyncMmk.syncProductPrices(dbProduct, child.children)
            break
          case 'extras':
            await SyncMmk.syncProductExtras(dbProduct, child.children)
            break
          case 'discounts':
            await SyncMmk.syncProductDiscounts(dbProduct, child.children)
            break
          default:
            console.log('Ignoring product child node ' + child.tag)
        }
      }
    }

    // If we have some old images, remove them
    total = dbProducts.length
    for (let i = 0; i < total; i++) {
      let dbProduct = dbProducts[i]
      if (!processedIds.includes(dbProduct.id)) {
        await dbProduct.discounts().delete()
        await dbProduct.extras().delete()
        await dbProduct.discounts().delete()
        await dbProduct.delete()
      }
    }
  }

  static async syncBoatPrices (boat, prices) {
    let processedIds = []
    let dbPrices = await boat.prices().fetch()
    dbPrices = dbPrices.rows

    let total = prices.length
    for (let i = 0; i < total; i++) {
      let price = prices[i]

      let dateFrom = SyncMmk.stringToDate(price.attrs.datefrom)
      let dateTo = SyncMmk.stringToDate(price.attrs.dateto)
      let priceAmount = SyncMmk.stringToFloat(price.attrs.price)

      if (dateTo < Date.now() || priceAmount === 0.0) {
        continue
      }

      let dbPrice = dbPrices[i]

      if (typeof dbPrice === 'undefined') {
        dbPrice = new MmkBoatPrice()
        dbPrice.boat_id = boat.id
      } else {
        processedIds.push(dbPrice.id)
      }

      dbPrice.date_from = dateFrom
      dbPrice.date_to = dateTo

      dbPrice.price = priceAmount
      dbPrice.currency = price.attrs.currency
      await dbPrice.save()
    }

    await SyncMmk.deleteOldRecords(dbPrices, processedIds)
  }

  static async syncProductPrices (product, prices) {
    let processedIds = []
    let dbPrices = await product.prices().fetch()
    dbPrices = dbPrices.rows

    let total = prices.length
    for (let i = 0; i < total; i++) {
      let price = prices[i]

      let dateFrom = SyncMmk.stringToDate(price.attrs.datefrom)
      let dateTo = SyncMmk.stringToDate(price.attrs.dateto)
      let priceAmount = SyncMmk.stringToFloat(price.attrs.price)

      if (dateTo < Date.now() || priceAmount === 0.0) {
        continue
      }

      let dbPrice = dbPrices[i]

      if (typeof dbPrice === 'undefined') {
        dbPrice = new MmkProductPrice()
        dbPrice.product_id = product.id
      } else {
        processedIds.push(dbPrice.id)
      }

      dbPrice.date_from = dateFrom
      dbPrice.date_to = dateTo
      dbPrice.price = priceAmount
      dbPrice.currency = price.attrs.currency
      await dbPrice.save()
    }

    await SyncMmk.deleteOldRecords(dbPrices, processedIds)
  }

  static async syncBoatImages (boat, images) {
    let processedIds = []
    let dbImages = await boat.images().fetch()
    dbImages = dbImages.rows

    let total = images.length
    for (let i = 0; i < total; i++) {
      let image = images[i]

      let href = image.attrs.href.replace('http:', 'https:')
      let dbImage = dbImages.find(function (item) {
        return item.href === href
      })

      if (typeof dbImage === 'undefined') {
        dbImage = new MmkBoatImage()
        dbImage.boat_id = boat.id
      } else {
        processedIds.push(dbImage.id)
      }

      dbImage.href = href
      dbImage.comment = image.attrs.comment
      await dbImage.save()
    }

    await SyncMmk.deleteOldRecords(dbImages, processedIds)
  }

  static async syncBoatEquipments (boat, equipments) {
    let processedIds = []
    let dbItems = await boat.equipments().fetch()
    dbItems = dbItems.rows

    let total = equipments.length
    for (let i = 0; i < total; i++) {
      let equipment = equipments[i]

      let mmkId = SyncMmk.stringToInt(equipment.attrs.id)
      let dbItem = dbItems.find(function (item) {
        return item.mmk_id === mmkId
      })

      if (typeof dbItem === 'undefined') {
        dbItem = new MmkBoatEquipment()
        dbItem.boat_id = boat.id
        dbItem.mmk_id = mmkId
      } else {
        processedIds.push(dbItem.id)
      }

      dbItem.name = equipment.attrs.name
      dbItem.category_name = equipment.attrs.categoryname
      dbItem.value = equipment.attrs.value
      dbItem.category_id = null

      let categoryId = SyncMmk.stringToInt(equipment.attrs.parentid)
      if (categoryId > -1) {
        let dbCategory = await MmkEquipmentCategory.findBy('mmk_id', categoryId)

        if (dbCategory !== null) {
          dbItem.category_id = dbCategory.id
        }
      }

      await dbItem.save()
    }

    await SyncMmk.deleteOldRecords(dbItems, processedIds)
  }

  static async syncBoatExtras (boat, extras) {
    let processedIds = []
    let dbExtras = await boat.extras().fetch()
    dbExtras = dbExtras.rows

    let total = extras.length
    for (let i = 0; i < total; i++) {
      let extra = extras[i]

      let validTo = SyncMmk.stringToDate(extra.attrs.validdateto)

      if (validTo < Date.now()) {
        continue
      }

      let mmkId = SyncMmk.stringToInt(extra.attrs.id)
      let dbExtra = dbExtras.find(function (item) {
        return item.mmk_id === mmkId
      })

      if (typeof dbExtra === 'undefined') {
        dbExtra = new MmkBoatExtra()
        dbExtra.boat_id = boat.id
        dbExtra.mmk_id = mmkId
      } else {
        processedIds.push(dbExtra.id)
      }

      await SyncMmk.fillExtraData(dbExtra, extra)

      await dbExtra.save()
    }

    await SyncMmk.deleteOldRecords(dbExtras, processedIds)
  }

  static async syncProductExtras (product, extras) {
    let processedIds = []
    let dbExtras = await product.extras().fetch()
    dbExtras = dbExtras.rows

    let total = extras.length
    for (let i = 0; i < total; i++) {
      let extra = extras[i]

      let validTo = SyncMmk.stringToDate(extra.attrs.validdateto)

      if (validTo < Date.now()) {
        continue
      }

      let mmkId = SyncMmk.stringToInt(extra.attrs.id)
      let dbExtra = dbExtras.find(function (item) {
        return item.mmk_id === mmkId
      })

      if (typeof dbExtra === 'undefined') {
        dbExtra = new MmkProductExtra()
        dbExtra.product_id = product.id
        dbExtra.mmk_id = mmkId
      } else {
        processedIds.push(dbExtra.id)
      }

      await SyncMmk.fillExtraData(dbExtra, extra)

      await dbExtra.save()
    }

    await SyncMmk.deleteOldRecords(dbExtras, processedIds)
  }

  static async fillExtraData(dbExtra, extra) {
    dbExtra.name = extra.attrs.name
    dbExtra.price = SyncMmk.stringToFloat(extra.attrs.price)
    dbExtra.currency = extra.attrs.currency

    let timeUnit = extra.attrs.timeunit
    if (timeUnit === '0') {
      dbExtra.time_unit = 'booking'
    } else if (timeUnit === '86400000') {
      dbExtra.time_unit = 'day'
    } else {
      dbExtra.time_unit = 'week'
    }

    dbExtra.per_person = extra.attrs.customquantity === '1'
    dbExtra.valid_from = SyncMmk.stringToDate(extra.attrs.validdatefrom)
    dbExtra.valid_to = SyncMmk.stringToDate(extra.attrs.validdateto)
    dbExtra.sailing_from = SyncMmk.stringToDate(extra.attrs.sailingdatefrom)
    dbExtra.sailing_to = SyncMmk.stringToDate(extra.attrs.sailingdateto)
    dbExtra.obligatory = extra.attrs.obligatory === '1'
    dbExtra.included_in_base_price = extra.attrs.includedinbaseprice === '1'
    dbExtra.payable_on_invoice = extra.attrs.payableoninvoice === '1'
    dbExtra.deposit_waiver = extra.attrs.includesdepositwaiver === '1'

    // Some extras are available only in specific base
    dbExtra.base_id = null

    let baseId = SyncMmk.stringToInt(extra.attrs.availableinbase)
    if (baseId !== -1) {
      let dbBase = await MmkBase.findBy('mmk_id', baseId)

      if (dbBase !== null) {
        dbExtra.base_id = dbBase.id
      }
    }
  }

  static async syncBoatDiscounts (boat, discounts) {
    let processedIds = []
    let dbDiscounts = await boat.discounts().fetch()
    dbDiscounts = dbDiscounts.rows

    let total = discounts.length
    for (let i = 0; i < total; i++) {
      let discount = discounts[i]

      let validTo = SyncMmk.stringToDate(discount.attrs.validdateto)

      if (validTo < Date.now()) {
        continue
      }

      let mmkId = SyncMmk.stringToInt(discount.attrs.id)
      let dbDiscount = dbDiscounts.find(function (item) {
        return item.mmk_id === mmkId
      })

      if (typeof dbDiscount === 'undefined') {
        dbDiscount = new MmkBoatDiscount()
        dbDiscount.boat_id = boat.id
        dbDiscount.mmk_id = mmkId
      } else {
        processedIds.push(dbDiscount.id)
      }

      await SyncMmk.fillDiscountData(dbDiscount, discount)

      await dbDiscount.save()
    }

    await SyncMmk.deleteOldRecords(dbDiscounts, processedIds)
  }

  static async syncProductDiscounts (product, discounts) {
    let processedIds = []
    let dbDiscounts = await product.discounts().fetch()
    dbDiscounts = dbDiscounts.rows

    let total = discounts.length
    for (let i = 0; i < total; i++) {
      let discount = discounts[i]

      let validTo = SyncMmk.stringToDate(discount.attrs.validdateto)

      if (validTo < Date.now()) {
        continue
      }

      let mmkId = SyncMmk.stringToInt(discount.attrs.id)
      let dbDiscount = dbDiscounts.find(function (item) {
        return item.mmk_id === mmkId
      })

      if (typeof dbDiscount === 'undefined') {
        dbDiscount = new MmkProductDiscount()
        dbDiscount.product_id = product.id
        dbDiscount.mmk_id = mmkId
      } else {
        processedIds.push(dbDiscount.id)
      }

      await SyncMmk.fillDiscountData(dbDiscount, discount)

      await dbDiscount.save()
    }

    await SyncMmk.deleteOldRecords(dbDiscounts, processedIds)
  }

  static async fillDiscountData (dbDiscount, discount) {
    dbDiscount.name = discount.attrs.name
    dbDiscount.percentage = SyncMmk.stringToFloat(discount.attrs.percentage)
    dbDiscount.valid_from = SyncMmk.stringToDate(discount.attrs.validdatefrom)
    dbDiscount.valid_to = SyncMmk.stringToDate(discount.attrs.validdateto)
    dbDiscount.sailing_from = SyncMmk.stringToDate(discount.attrs.sailingdatefrom)
    dbDiscount.sailing_to = SyncMmk.stringToDate(discount.attrs.sailingdateto)
    dbDiscount.valid_days_min = SyncMmk.stringToInt(discount.attrs.validdaysfrom) / 86400000
    dbDiscount.valid_days_max = SyncMmk.stringToInt(discount.attrs.validdaysto) / 86400000
    dbDiscount.discount_type = SyncMmk.stringToInt(discount.attrs.discounttype)
    dbDiscount.included_in_base_price = discount.attrs.includedinbaseprice === '1'
    dbDiscount.excludes_others = discount.attrs.excludesotherdiscounts === '1'
    dbDiscount.affected_by_max_value = discount.attrs.affectedbymaximum === '1'

    let baseId = SyncMmk.stringToInt(discount.attrs.availableinbase)
    if (baseId !== -1) {
      let dbBase = await MmkBase.findBy('mmk_id', baseId)
      Database.close()

      if (dbBase !== null) {
        dbDiscount.base_id = dbBase.id
      }
    }
  }

  static async syncBoatLocations (boat, locations) {
    let processedIds = []
    let dbLocations = await boat.locations().notDefaultBase().fetch()
    dbLocations = dbLocations.rows

    let total = locations.length
    for (let i = 0; i < total; i++) {
      let location = locations[i]

      let mmkId = SyncMmk.stringToInt(location.attrs.baseid)
      let dateFrom = SyncMmk.stringToDate(location.attrs.datefrom)
      let dateTo = SyncMmk.stringToDate(location.attrs.dateto)

      if (dateTo < Date.now()) {
        continue
      }

      let dbBase = await MmkBase.findBy('mmk_id', mmkId)
      Database.close()

      if (dbBase === null) {
        console.error('Could not find base with ID ' + mmkId)
        continue
      }

      let dbLocation = dbLocations[i]

      if (typeof dbLocation === 'undefined') {
        dbLocation = new MmkBoatLocation()
        dbLocation.boat_id = boat.id
      } else {
        processedIds.push(dbLocation.id)
      }

      dbLocation.base_id = dbBase.id
      dbLocation.date_from = dateFrom
      dbLocation.date_to = dateTo
      dbLocation.default_base = false
      await dbLocation.save()
    }

    await SyncMmk.deleteOldRecords(dbLocations, processedIds)
  }

  static async syncBoatCategories (boat, categories) {
    let processedIds = []
    let dbCategories = await boat.categories().fetch()
    dbCategories = dbCategories.rows

    let total = categories.length
    for (let i = 0; i < total; i++) {
      let category = categories[i]

      let endDate = SyncMmk.stringToDate(category.attrs.enddate)
      if (endDate < Date.now()) {
        continue
      }

      let name = category.attrs.name
      let dbCategory = dbCategories.find(function (item) {
        return item.name === name
      })

      if (typeof dbCategory === 'undefined') {
        dbCategory = new MmkBoatCategory()
        dbCategory.boat_id = boat.id
      } else {
        processedIds.push(dbCategory.id)
      }

      dbCategory.name = name
      dbCategory.start_date = SyncMmk.stringToDate(category.attrs.startdate)
      dbCategory.end_date = endDate
      await dbCategory.save()
    }

    await SyncMmk.deleteOldRecords(dbCategories, processedIds)
  }

  static async deleteOldRecords (dbRecords, idsToKeep) {
    let total = dbRecords.length
    for (let i = 0; i < total; i++) {
      let dbRecord = dbRecords[i]
      if (!idsToKeep.includes(dbRecord.id)) {
        await dbRecord.delete()
      }
    }
  }

  static async deleteBoat (boat) {
    console.log('Removing boat from database: ' + boat.name + ' / ' + boat.mmk_id)

    let products = await boat.products().fetch()
    let total = products.rows.length

    for (let i = 0; i < total; i++) {
      let product = products.rows[i]
      await product.prices().delete()
      await product.discounts().delete()
      await product.extras().delete()
    }

    await boat.discounts().delete()
    await boat.equipments().delete()
    await boat.extras().delete()
    await boat.images().delete()
    await boat.locations().delete()
    await boat.prices().delete()
    await boat.products().delete()
    await boat.delete()
  }

  static stringToDate (stringified) {
    let parts = stringified.split('-')
    return new Date(parts[0], parts[1] - 1, parts[2])
  }

  static stringToInt (stringified) {
    let result = parseInt(stringified)
    if (isNaN(result)) {
      return null
    } else {
      return result
    }
  }

  static stringToFloat (stringified) {
    let result = parseFloat(stringified)
    if (isNaN(result)) {
      return null
    } else {
      return result
    }
  }
}

module.exports = SyncMmk
