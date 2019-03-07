'use strict'
const Page = use('App/Models/Page')
const PageLinks = use('App/Models/PageLink')
const Database = use('Database')
const Destinations = use('App/Services/Destinations')
const BoatService = use('App/Services/Boats')
const _ = use('lodash')

class PageController {
  /**
   * @description Groups pages together by their "type" e.g. blog, sailing-itineraries etc
   * after grouping then sort by popular e.g. croatia, greece and bvi (in no particular order)
   * 
   * @param {any} { request, params } 
   * @returns 
   * @memberof PageController
   */
  async indexByType ({ request, params }) {
    const pages = await Page
      .query()
      .setVisible(['id', 'title', 'description', 'link'])
      .where('type', params.type)
      .with('user', (builder) => {
        builder.setVisible(['fullName'])
      })
      .with('country')
      .with('pageImage', (builder) => {
        builder.setVisible(['fileName', 'altText', 'titleText'])
      })
      .orderBy('created_at', 'desc')
      .fetch()

    const grouped = _.reduce(pages.toJSON(), function (result, value, key) {
      value.popular = (value.country) ? value.country.Popular : 0
      if (value.country) {
        (result[value.country.Name] || (result[value.country.Name] = [])).push(value)
      } else {
        (result['Global'] || (result['Global'] = [])).push(value)
      }
      return result
    }, {})
    const sorted = {}

    const keysSorted = Object.keys(grouped).sort(function (a, b) {
      return grouped[a][0].popular - grouped[b][0].popular
    })

    for (const key of keysSorted.reverse()) {
      sorted[key] = grouped[key]
    }

    return {
      groups: sorted
    }
  }
  /**
   * @description returns a page by its slug, and then loads boats and related pages
   * 
   * @param {any} { request, response, params } 
   * @returns 
   * @memberof PageController
   */
  async index ({ request, response, params }) {
    let page = await Page
      .query()
      .where('slug', params.slug)
      .with('user')
      .with('pageCategory')
      .with('pageImage')
      .with('pageChequer')
      .with('pageBoatBrand')
      .with('pageBoatType')
      .first()

    if (!page) {
      return response.status(404).send('Not Found')
    }

    // now we can use page data throughout the controller method
    page = page.toJSON()

    const boatInfo = {
      baseId: page.base_id,
      areaId: page.area_id,
      countryId: page.country_id,
      boatBrand: _.map(page.pageBoatBrand, 'brand_id'),
      boatType: _.map(page.pageBoatType, 'type_id')
    }

    const { locationType, tableType } = Destinations.locationToLowestType(page)
    let location = false
    let region = false

    if (locationType) {
      region = await Database.from('regions')
        .select('base_id', 'base_name', 'area_id', 'area_name_en as area_name', 'country_id', 'country_name_en as country_name')
        .where(locationType, page[locationType])
        .first()

      location = {
        name: region[tableType],
        id: region[locationType]
      }
    }

    const itineraries = Page
      .query()
      .setVisible(['id', 'title', 'description', 'link'])
      .whereNot('id', page.id) // duplicate
      .where('type', 'sailing-itineraries')
      .with('user', (builder) => {
        builder.setVisible(['fullName'])
      })
      .with('pageImage', (builder) => {
        builder.setVisible(['fileName', 'altText', 'titleText'])
      })
      // .where('country_id', page.country_id)
      .orderBy('created_at', 'desc')

    if (locationType) {
      itineraries.where('country_id', page.country_id)
    }

    const boatData = await BoatService.searchBase(boatInfo)

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
      location: location,
      region: region,
      destinations: await Destinations.popularLocations(),
      page: page,
      boats: boatData.boats,
      pages: {
        sailingItineraries: await itineraries.fetch(),
        blogs: false,
        lastMinutes: false
      },
      pageLinks: pageLinks
    }
  }
}

module.exports = PageController
