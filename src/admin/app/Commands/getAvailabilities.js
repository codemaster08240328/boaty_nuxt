'use strict'

const { Command } = require('@adonisjs/ace')
const Database = use('Database')
const querystring = require('querystring')
const _ = use('lodash')
const SCHelpers = use('App/Services/Helpers')
const Env = use('Env')

class getAvailabilities extends Command {
  static get signature () {
    return `
      bbapi:getAvailabilities
    `
  }

  static get description () {
    return 'Update boat equipment, extras and images'
  }

  async handle (args, options) {
    await this.getAvailabilities()

    console.log('completed')

    Database.close()
  }

  async getAvailabilities () {
    /*
      Use only the minimum params, to get full listings
    */
    const form = {
      username: Env.get('BB_USERNAME'),
      password: Env.get('BB_PASSWORD'),
      lang: 'en',
      loadAvailability: true,
      stateDate: new Date().toJSON().slice(0, 10).replace(/-/g, '/')
    }
    console.log(form)
    const options = {
      headers: {
        'content-type': 'application/x-www-form-urlencoded',
        'Accept-Encoding': 'gzip,deflate'
      },
      gzip: true,
      timeout: 60 * 30 * 1000
    }

    // get a list of a
    const BoatIDS = await Database.select('ID').from('boats').where('status', 1)

    let index = 0

    while (BoatIDS.length > index) {
      let togo = BoatIDS.length - index
      console.log('to go: ' + togo)
      form.boatid = BoatIDS[index].ID
      options.url = 'https://api.boatbooker.net/ws/boatinfo/getboatinfo?' + querystring.stringify(form)

      try {
        const data = await SCHelpers.Request(options)
        let Periods = data.Availability

        if (Periods.length !== 0) {
          // clean up periods
          Periods = _.map(Periods, (p) => {
            p.BoatID = form.boatid
            p.CurrencyCode = 'EUR'
            p.From = p.DateFrom
            p.To = p.DateTo
            p.AvailabilityStatus = p.Status
            p.BasePrice = p.PriceWithoutDiscount

            delete p.PriceWithoutDiscount
            delete p.Status
            delete p.Discount
            delete p.DateFrom
            delete p.DateTo
            delete p.Currency
            return p
          })

          await Database.insertUpdate('boat_periods', Periods)
        }
      } catch (e) {
        console.log(e)
      }
      index++
    }

    return true
  }
}

module.exports = getAvailabilities
