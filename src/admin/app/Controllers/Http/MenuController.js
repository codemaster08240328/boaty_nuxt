'use strict'
const Page = use('App/Models/Page')
const _ = use('lodash')
const Redis = use('Redis')
const SCHelpers = use('App/Services/Helpers')

class MenuController {
  constructor () {
    this.options = {
      headers: {
        // 'content-type' : 'application/x-www-form-urlencoded',
        // 'Accept-Encoding' : 'gzip,deflate',
        'content-type': 'application/json'
      },
      rejectUnauthorized: false,
      requestCert: true,
      agent: false,
      method: 'GET'
      // gzip: true
    }
  }
  /**
   * @description loads data required for every page load via nuxtServerInit
   * 
   * @param {object} { request } 
   * @returns 
   * @memberof MenuController
   */
  async index ({ request }) {
    /*
      Yacht charter
    */
    // const yachtCharterKey = 'menu-yacht-charter'
    // let yachtCharters = await Redis.get(yachtCharterKey)
    // if (yachtCharters) {
    //   const pages = await Page
    //     .query()
    //     .setVisible(['id', 'title', 'description', 'link'])
    //     .where('type', 'yacht-charter')
    //     .with('country')
    //     .with('pageImage', (builder) => {
    //       builder.setVisible(['fileName', 'altText', 'titleText'])
    //     })
    //     .orderBy('created_at', 'desc')
    //     .fetch()

    //   const grouped = _.reduce(pages.toJSON(), function (result, value, key) {
    //     value.popular = (value.country) ? value.country.Popular : 0
    //     if (value.country) {
    //       (result[value.country.Name] || (result[value.country.Name] = [])).push(value)
    //     } else {
    //       (result['Global'] || (result['Global'] = [])).push(value)
    //     }
    //     return result
    //   }, {})
    //   const sorted = {}

    //   const keysSorted = Object.keys(grouped).sort(function (a, b) {
    //     return grouped[a][0].popular - grouped[b][0].popular
    //   })

    //   for (const key of keysSorted.reverse()) {
    //     sorted[key] = grouped[key]
    //   }

    //   await Redis.set(yachtCharterKey, JSON.stringify(sorted))
    //   yachtCharters = sorted
    // } else {
    //   yachtCharters = JSON.parse(yachtCharters)
    // }
    /*
      Itineraries 
    */
    const itineraryKey = 'menu-sailing-itineraries'
    let sailingItineraries = await Redis.get(itineraryKey)
    if (!sailingItineraries) {
      const pages = await Page
        .query()
        .setVisible(['id', 'title', 'description', 'link'])
        .where('type', 'sailing-itineraries')
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

      await Redis.set(itineraryKey, JSON.stringify(sorted))
      sailingItineraries = sorted
    } else {
      sailingItineraries = JSON.parse(sailingItineraries)
    }

    const ratesKey = 'menu-rates'
    let fxRates = await Redis.get(ratesKey)

    if (!fxRates) {
      this.options.url = 'https://s3.eu-west-2.amazonaws.com/sc30/uploads/json/rates.json'
      try {
        fxRates = await SCHelpers.Request(this.options)
      } catch (e) {
        // return e
      }

      await Redis.set(ratesKey, JSON.stringify(fxRates))
    } else {
      fxRates = JSON.parse(fxRates)
    }

    return {
      sailingItineraries: sailingItineraries,
      // yachtCharters: yachtCharters,
      fxRates: fxRates
    }
  }
}

module.exports = MenuController
