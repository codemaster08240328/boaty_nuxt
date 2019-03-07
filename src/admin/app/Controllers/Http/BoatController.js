'use strict'
const Boat = use('App/Models/Boat')
const BoatModelImage = use('App/Models/BoatModelImage')
const BoatPeriod = use('App/Models/BoatPeriod')
const BoatImage = use('App/Models/BoatImage')
const BoatEquipment = use('App/Models/BoatEquipment')
const BoatExtra = use('App/Models/BoatExtra')
const BoatReview = use('App/Models/BoatReview')
const BoatBase = use('App/Models/BoatBase')
const PageService = use('App/Services/Pages')
const _ = use('lodash')
const BoatService = use('App/Services/Boats')
const Database = use('Database')
const Redis = use('Redis')
const URL = use('App/Services/Url')

class BoatController {
  async index ({ request, params, response }) {
    let result = {}
    const BoatID = params.boat_id

    result.boat = await Boat.find(BoatID)
    let images = await BoatImage.query().where('BoatID', BoatID).fetch()

    if (images.toJSON().length === 0) {
      images = await BoatModelImage.query().where('BoatID', BoatID).fetch()
    }

    let periodsModel = await BoatPeriod.query().where('BoatID', BoatID).fetch()
    result.boat.periods = periodsModel
    result.boat.periods_calendar = BoatService.sortPeriods(periodsModel.toJSON())

    let equipmentModel = await BoatEquipment.query().where('BoatID', BoatID).fetch()
    result.boat.equipment = BoatService.sortEquipment(equipmentModel.toJSON())

    result.boat.extras = await BoatExtra
      .query()
      .with('BoatExtraPrice', (builder) => {
        builder.where('BoatID', BoatID)
      })
      .where('BoatID', BoatID)
      .fetch()

    // don't need models now, tojson
    images = images.toJSON()

    result.boat.images = images

    if (result.boat.images.length > 0) {
      /*
        The last image (either boat_model_images or boat_images) always contains a layout picture (or it should)
      */
      result.boat.layout_image = images.splice(-1, 1)[0].url
    }

    /*
      The first image (as above) always contains a full picture of the boat, usually sailing
      great for the main image, used in twitter, fb, structured data etc
    */
    // result.boat.main_image = images.splice(0, 1)[0].url
    result.boat.boat_bases = await BoatBase.query().where('BoatID', BoatID).fetch()
    result.boat.primary_base = _.each(result.boat.boat_bases.toJSON(), (value, index) => {
      if (value.isPrimary === 1) {
        return value
      }
    })

    result.boat.reviews = await BoatReview.query().where('BoatID', BoatID).where('status', 1).fetch()

    let regionData = await Database.from('regions')
      .select('base_id', 'base_coordinates', 'base_name', 'area_id', 'area_name_en as area_name', 'country_id', 'country_name_en as country_name')
      .where('base_id', result.boat.primary_base[0].ID)

    result.boat.location = regionData[0]
    result.boat.location.coordinates = {
      lat: result.boat.location.base_coordinates.split(',')[0],
      lng: result.boat.location.base_coordinates.split(',')[1]
    }

    result.boat.location.urls = [
      URL.stringToUrl({ urlParts: [result.boat.location.country_name] }),
      URL.stringToUrl({ urlParts: [result.boat.location.country_name, result.boat.location.area_name] }),
      URL.stringToUrl({ urlParts: [result.boat.location.country_name, result.boat.location.area_name, result.boat.location.base_name] })
    ]

    result.wp = {}
    result.wp.blog = await Redis.get(`wp-blog-${result.boat.location.country_name}`.toLocaleLowerCase())
    result.wp.blog = JSON.parse(result.wp.blog)
    result.wp.last_minute = await Redis.get(`wp-last-minute-${result.boat.location.country_name}`.toLowerCase())
    result.wp.last_minute = JSON.parse(result.wp.last_minute)

    result.pages = {}
    result.pages.sailingItineraries = await PageService.cards({ type: 'sailing-itineraries', countryId: regionData[0].country_id })
    // result.wp.sailing_itinerary = await Redis.get(`wp-sailing-itinerary-${result.boat.location.country_name}`.toLowerCase())
    // result.wp.sailing_itinerary = JSON.parse(result.wp.sailing_itinerary)

    return result
  }
}

module.exports = BoatController
