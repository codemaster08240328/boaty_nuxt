'use strict'
const Database = use('Database')
const Url = use('App/Services/Url')
const Destinations = use('App/Services/Destinations')
const Redis = use('Redis')

class HomeController {
  async index ({ request }) {
    const redisKey = 'homepage'
    const homePage = await Redis.get(redisKey)

    if (homePage) {
      return JSON.parse(homePage)
    }

    let gmapData = await Database.from('v1_gmap_markers').select('*')
    let results = {
      gmap: [],
      popularLocations: []
    }
    let l = gmapData.length
    let coords

    while (l--) {
      let entry = {}
      entry.country = gmapData[l].country_name_en
      entry.area = gmapData[l].area_name_en
      entry.base = gmapData[l].base_name

      entry.countryUrl = Url.stringToUrl({ urlParts: [entry.country], prefix: 'search' })
      entry.areaUrl = Url.stringToUrl({ urlParts: [entry.country, entry.area], prefix: 'search' })
      entry.baseUrl = Url.stringToUrl({ urlParts: [entry.country, entry.area, entry.base], prefix: 'search' })
      coords = gmapData[l].base_coordinates.split(',')
      entry.count = gmapData[l].boats_count
      entry.priceFrom = gmapData[l].PriceWithDiscount
      entry.position = {
        lat: parseFloat(coords[0]),
        lng: parseFloat(coords[1])
      }

      results.gmap.push(entry)
    }

    results.popularLocations = await Destinations.popularLocations()
    await Redis.set(redisKey, JSON.stringify(results))
    return results
  }
}

module.exports = HomeController
