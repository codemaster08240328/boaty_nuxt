'use strict'

const { Command } = require('@adonisjs/ace')
const SCHelpers = use('App/Services/Helpers')
const Database = use('Database')
const querystring = require('querystring')
const Redis = use('Redis')
const Mail = use('Mail')

class WP extends Command {
  constructor () {
    super()
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

    this.pageTypes = [
      {
        name: 'sailing-itinerary',
        url: 'sailing itinerary',
        post_type: 'pages'
      },
      {
        name: 'last-minute',
        url: 'last minute',
        post_type: 'pages'
      },
      {
        name: 'blog',
        url: '',
        post_type: 'posts'
      }
    ]

    this.countriesWithDifferentName = {
      'st. martin': 'saint martin',
      'st. vincent': 'saint vincent and the grenadines'
    }

    this.countries = []
  }

  static get signature () {
    return `
      wp:getWPPosts
    `
  }

  static get description () {
    return 'Retrieves data from WP and caches into Redis'
  }

  async handle (args, options) {
    this.countries = await Database.table('countries').select('Name')

    await this.getWPSailingItinerariesByCountry()

    const data = {
      message: 'Cached WP posts in Redis'
    }

    await Mail.send('emails.cron', data, (message) => {
      message
        .to('matt@saowapan.com')
        .from('info@sailchecker.com')
        .subject('Updated WP Redis Cache')
    })

    Redis.quit('local')
    Database.close()
  }

  async getWPSailingItinerariesByCountry () {
    for (let item of this.countries) {
      for (let type of this.pageTypes) {
        let query = {
          country: item.Name,
          blog_relation: type.url,
          per_page: 4
        }

        this.options.url = `https://nginx/wp-json/wp/v2/${type.post_type}?${querystring.stringify(query)}`

        let data = []
        let redisKey = `wp-${type.name}-${item.Name}`.toLowerCase()

        try {
          data = await SCHelpers.Request(this.options)

          if (data.length > 0) {
            await Redis.set(redisKey, JSON.stringify(data))
          }
        } catch (e) {
          console.log(e)
        }
      }
    }
    return true
  }

  async getWPLastMinuteByCountry () {

  }

  async getWPBlogByCountry () {

  }
}

module.exports = WP
