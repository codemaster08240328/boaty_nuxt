'use strict'
const Database = use('Database')
const Url = use('App/Services/Url')
const Destinations = use('App/Services/Destinations')
const BoatService = use('App/Services/Boats')

class SeoController {
  /*
    Creates data for gmap markers
  */
  async gmaps () {
    let data = await Database.from('v1_gmap_markers').select('*')

    let results = []
    let l = data.length
    let coords

    while (l--) {
      let entry = {}
      entry.country = data[l].country_name_en
      entry.area = data[l].area_name_en
      entry.base = data[l].base_name

      entry.countryUrl = Url.stringToUrl({ urlParts: [entry.country], prefix: 'yacht-charter' })
      entry.areaUrl = Url.stringToUrl({ urlParts: [entry.country, entry.area], prefix: 'yacht-charter' })
      entry.baseUrl = Url.stringToUrl({ urlParts: [entry.country, entry.area, entry.base], prefix: 'yacht-charter' })
      coords = data[l].base_coordinates.split(',')
      entry.count = data[l].boats_count
      entry.priceFrom = data[l].PriceWithDiscount
      entry.position = {
        lat: parseFloat(coords[0]),
        lng: parseFloat(coords[1])
      }

      results.push(entry)
    }

    return results
  }

  async pages ({ params }) {
    const result = await Database.raw(`
      SELECT 
          rwb.*
      FROM
          pages p
              LEFT JOIN
          v1_regions_with_boats rwb ON rwb.country_id = p.country_id
      WHERE
          p.type = 'yacht-charter'
      GROUP BY p.country_id   
    `)
    return result
  }

  /*
   used for sitemap and /yacht-charter/ page, creates a list of all regions with valid boats, valid being available (according to bb api)
  */
  async destinations () {
    let data = await Database.from('v1_regions_with_boats').select('*')
    return Destinations.sortDestinationsToArray(data)
  }
  /*
    Used for sitemap, creates complete list of boats, this actually needs to be improved so it only lists valid boats
  */
  async boats () {
    const boatInfo = {
      page: 1,
      limit: 20000 // TODO: should probably do this differently...
    }

    const boatData = await BoatService.searchBase(boatInfo)

    let boatUrls = []
    for (let boat of boatData.boats) {
      boatUrls.push('/boat' + Url.stringToUrl({ urlParts: [ boat.BrandName, boat.ModelName + '-' + boat.ID ] }))
    }

    return boatUrls
  }

  /*
    used for sitemap, _with_boats checks for regions that have valid boats, valid being available (according to bb api) and of sufficient quality
  */
  async search () {
    let data = await Database.from('v1_regions_with_boats').select('*')
    return Destinations.sortDestinationsToArray(data, 'search')
  }

  /*
    Uses the "Popular" flag on country table to get a list of countries with an image, text etc for SEO
  */
  async popularLocations () {
    return Destinations.popularLocations()
  }
}

module.exports = SeoController
