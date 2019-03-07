'use strict'
const Page = use('App/Models/Page')
const Redis = use('Redis')
const Antl = use('Antl')
const BoatService = use('App/Services/Boats')
const PageService = use('App/Services/Pages')
const _ = use('lodash')

class SearchController {
  async index ({ request }) {
    let filterCookie = request.plainCookie('search')
    let boatInfo = {
      prices: [[2000, 4000]],
      cabins: [2, 3]
    }
    let search = {
      prices: [[2000, 4000]],
      cabins: [2, 3]
    }

    if (filterCookie) {
      try {
        boatInfo = _.assign(boatInfo, JSON.parse(filterCookie))
        search = _.assign(search, JSON.parse(filterCookie))
        if (!_.isObject(boatInfo)) {
          boatInfo = {
            prices: [[2000, 4000]],
            cabins: [2, 3]
          }
          search = {
            prices: [[2000, 4000]],
            cabins: [2, 3]
          }
        }
      } catch (e) {
      }
    }

    // if (boatInfo.country) {
    //   delete boatInfo.country
    //   delete search.country
    // }

    // If no country exists in the post, assume the client wants boats from any (every) location
    if (!request.region) {
      boatInfo.page = 1
      boatInfo.limit = 18
      boatInfo.paginate = true

      const boatData = await BoatService.searchBase(boatInfo)
      return {
        boats: boatData.boats,
        boats_paginate: boatData.paginate,
        title: Antl.forLocale(request.locale).formatMessage('search.titleNoLocation'),
        search: search
      }
    }

    // retrieve wp posts/pages by country TODO make this better, use area and base etc (smarter)
    const blogs = await Redis.get(`wp-blog-${request.region.data.country_name_en}`.toLocaleLowerCase())
    const lastMinute = await Redis.get(`wp-last-minute-${request.region.data.country_name_en}`.toLowerCase())

    boatInfo[request.region.locationId.camelCase] = request.region.locationId.id
    boatInfo.page = 1
    boatInfo.limit = 18
    boatInfo.paginate = true
    const boatData = await BoatService.searchBase(boatInfo)

    const page = Page.query()
      .where(request.region.locationId.name, request.region.locationId.id)
      .where('type', 'search')
      .on('query', console.log)
      .with('pageImage')

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
      location: request.region,
      boats: boatData.boats,
      boats_paginate: boatData.paginate,
      title: Antl.forLocale(request.locale).formatMessage('search.title', { location: request.region.name }),
      wp: {
        blog: JSON.parse(blogs),
        last_minute: JSON.parse(lastMinute)
        // sailing_itinerary: JSON.parse(sailingItinerary)
      },
      pages: {
        sailingItineraries: await PageService.cards({ type: 'sailing-itineraries', countryId: request.region.data.country_id })
      },
      page: await page.first(),
      search: search
    }
  }

  async getBoatsWithFilters ({ request }) {
    const data = request.all()

    const boatInfo = {
      boatType: (data.boatType) ? data.boatType : [],
      date: data.date || '',
      prices: data.prices || [],
      lengths: data.lengths || [],
      cabins: data.cabins || [],
      years: data.years || [],
      toilets: data.toilets || [],
      page: 1,
      limit: 18,
      orderBy: (data.sortby) ? parseInt(data.sortby) : false,
      paginate: true
    }

    if (request.region) {
      boatInfo[request.region.locationId.camelCase] = request.region.locationId.id // country, area, base_id etc
    }

    const boatData = await BoatService.searchBase(boatInfo)
    return {
      boats_paginate: boatData.paginate,
      boats: boatData.boats
    }
  }
}

module.exports = SearchController
