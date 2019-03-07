'use strict'
const Database = use('Database')
const BoatService = use('App/Services/Boats')
const PageService = use('App/Services/Pages')
const Page = use('App/Models/Page')
const Destinations = use('App/Services/Destinations')
const Redis = use('Redis')

class YachtCharterController {
  async index ({ request }) {
    let destinations = await Database.select('base_name', 'area_name_en', 'country_name_en', 'Popular')
      .from('regions')
      .leftJoin('countries', 'countries.ID', 'regions.country_id')
      .where('regions.country_id', request.region.data.country_id)

    let blog = await Redis.get(`wp-blog-${request.region.data.country_name_en}`.toLocaleLowerCase())
    let lastMinute = await Redis.get(`wp-last-minute-${request.region.data.country_name_en}`.toLowerCase())
    let sailingItinerary = await Redis.get(`wp-sailing-itinerary-${request.region.data.country_name_en}`.toLowerCase())
    const boatInfo = {
      // country, area, base_id etc
      [request.region.locationId.camelCase]: request.region.locationId.id
    }
    const boatData = await BoatService.searchBase(boatInfo)

    const page = Page.query()
      .where(request.region.locationId.name, request.region.locationId.id)
      .where('type', 'yacht-charter')
      .with('pageImage')
      .with('pageChequer')

    /**
     * if the yacht charter page is a country or area, only select the page where other locations are null
     * for e.g. a croatia > zadar page would have a country_id and an area_id but on a croatia page
     * we would only want to select by country_id
     */
    if (request.region.locationType === 'country') {
      page.whereRaw('IFNULL(area_id, 0) = 0')
      page.whereRaw('IFNULL(base_id, 0) = 0')
    } else if (request.region.locationType === 'area') {
      page.whereRaw('IFNULL(base_id, 0) = 0')
    }

    return {
      boats: boatData.boats,
      location: request.region,
      topDestinations: await Destinations.popularLocations(),
      page: await page.first(),
      wp: {
        blog: JSON.parse(blog),
        last_minute: JSON.parse(lastMinute)
        // sailing_itinerary: JSON.parse(sailingItinerary)
      },
      pages: {
        sailingItineraries: await PageService.cards({ type: 'sailing-itineraries', countryId: request.region.data.country_id })
      },
      destinations: Destinations.sortDestinationsToArray(destinations, 'search')
    }
  }
}

module.exports = YachtCharterController
