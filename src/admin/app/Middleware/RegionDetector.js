'use strict'
const Database = use('Database')
const Url = use('App/Services/Url')
const _ = use('lodash')
/**
 * 
 * 
 * @class RegionDetector
 */
class RegionDetector {
  async handle ({ request }, next) {
    let data = request.all()

    if (_.isEmpty(data, true) && request.plainCookie('search')) {
      data = request.plainCookie('search')
      data = JSON.parse(data)
    }

    const locationTypes = [
      'country',
      'area',
      'base'
    ]

    const tableTypes = {
      country: 'country_name_en',
      area: 'area_name_en',
      base: 'base_name'
    }

    let locationType = false

    for (let type of locationTypes) {
      if (data[type]) {
        locationType = type
      } else {
        break
      }
    }

    if (locationType !== false) {
      const region = await Database.from('regions')
        .select(
          'base_id',
          'base_name',
          'area_id',
          'area_name_en',
          'country_id',
          'country_name_en')
        .where(tableTypes[locationType], Url.urlSectionToString({ section: data[locationType] })).first()

      request.region = {
        data: region,
        locationType: locationType,
        name: region[tableTypes[locationType]],
        locationId: {
          name: `${locationType}_id`,
          id: region[`${locationType}_id`],
          camelCase: `${locationType}Id`
        }
      }
    }

    // call next to advance the request
    await next()
  }
}

module.exports = RegionDetector
