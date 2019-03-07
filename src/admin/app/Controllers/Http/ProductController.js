'use strict'
const Page = use('App/Models/Page')
const PageLinks = use('App/Models/PageLink')
// const Database = use('Database')
const Destinations = use('App/Services/Destinations')
const BoatService = use('App/Services/Boats')
const _ = use('lodash')

class PageController {
  /**
   * @description returns a page by its slug and if not slug then gets the page without any location data, and then loads boats and related pages
   * 
   * @param {any} { request, response, params } 
   * @returns 
   * @memberof PageController
   */
  async index ({ request, response, params }) {
    let page = Page
      .query()

    if (params.slug) {
      page.where('slug', params.slug)
    } else {
      // page.whereIn('id', [])
    }

    page.where('type', params.type)
      .where('status', 1)
      .with('user')
      .with('pageCategory')
      .with('pageImage', (builder) => {
        builder.orderBy('position', 'desc')
      })
      .with('pageChequer')
      .with('pageBoatBrand')
      .with('pageBoatType')

    page = await page.first()

    if (!page) {
      return response.status(404).send('Not Found')
    }

    let filterCookie = request.plainCookie('search')

    let boatInfo = {
      boatBrand: _.map(page.pageBoatBrand, 'brand_id'),
      boatType: _.map(page.pageBoatType, 'type_id'),
      page: 1,
      limit: 18,
      paginate: true
    }

    if (!page.country_id && !page.area_id && !page.base_id && request.region) {
      boatInfo[request.region.locationId.camelCase] = request.region.locationId.id
    } else if (page.country_id) {
      boatInfo.countryId = page.country_id
    } else if (page.area_id) {
      boatInfo.areaId = page.area_id
    } else if (page.base_id) {
      boatInfo.baseId = page.base_id
    }

    let search = {
      prices: [[2000, 4000]],
      cabins: [2, 3]
    }

    if (filterCookie) {
      try {
        search = _.assign(JSON.parse(filterCookie), search)
        boatInfo = _.assign(JSON.parse(filterCookie), boatInfo)
      } catch (e) {
      }
    }

    if (page.type === 'crewed-charter' || page.type === 'catamaran-charter') {
      boatInfo.boatType = [5]
      search.boatType = [5]
      boatInfo.cabins = [4]
      search.cabins = [4]
    } else if (page.type === 'bareboat-charter') {
      boatInfo.boatType = [4, 5]
      search.boatType = [4, 5]
      boatInfo.cabins = [2, 3, 4]
      search.cabins = [2, 3, 4]
    } else if (page.type === 'luxury-yacht-charter') {
      boatInfo.boatType = [6, 5]
      search.boatType = [6, 5]
      boatInfo.cabins = [4, 5]
      search.cabins = [4, 5]
      search.prices = [['2000', '4000'], ['4000', '6000'], ['6000', '10000']]
      boatInfo.prices = [['2000', '4000'], ['4000', '6000'], ['6000', '10000']]
    }

    const boatData = await BoatService.searchBase(boatInfo)

    const itineraries = Page
      .query()
      .setVisible(['id', 'title', 'description', 'link'])
      .whereNot('id', page.id) // duplicate
      .where('type', 'sailing-itineraries')
      .with('user', (builder) => {
        builder.setVisible(['fullName'])
      })
      .with('pageBoatType')
      .leftJoin('page_boat_types', 'pages.id', 'page_boat_types.page_id')
      .whereIn('page_boat_types.type_id', boatInfo.boatType)
      .with('pageImage', (builder) => {
        builder.setVisible(['fileName', 'altText', 'titleText'])
      })
      .orderBy('created_at', 'desc')
      .groupBy('pages.id')

    if (page.country_id) {
      itineraries.where('country_id', page.country_id)
    }

    let links = await PageLinks
      .query()
      .leftJoin('link_groups', 'link_groups.id', 'page_links.group_id')
      .whereRaw('group_id IN (select group_id from page_links pl where pl.page_id = ?)', [page.id])
      .with('page', (builder) => {
        builder.setVisible(['title', 'slug', 'id', 'link'])
      })
      .fetch()

    links = links.toJSON()
    const linkGroups = _.reduce(links, function (result, value) {
      (result[value.name] || (result[value.name] = [])).push(value)
      return result
    }, {})

    const pageLinks = []
    _.each(linkGroups, (value, index) => {
      const currentIndex = _.findIndex(value, (o) => { return o.page_id === page.id })
      pageLinks.push({
        name: index,
        all: value,
        previous: value[currentIndex - 1],
        current: value[currentIndex],
        next: value[currentIndex + 1]
      })
    })

    return {
      region: request.region,
      destinations: await Destinations.popularLocations(),
      page: page,
      boats: boatData.boats,
      boats_paginate: boatData.paginate,
      search: search,
      pages: {
        sailingItineraries: await itineraries.orderBy('title', 'desc').limit(4).fetch(),
        blogs: false,
        lastMinutes: false
      },
      pageLinks: pageLinks
    }
  }
}

module.exports = PageController
