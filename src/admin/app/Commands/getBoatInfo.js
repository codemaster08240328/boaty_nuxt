'use strict'

const { Command } = require('@adonisjs/ace')
const SCHelpers = use('App/Services/Helpers')
const Database = use('Database')
const querystring = require('querystring')
const _ = use('lodash')
const Env = use('Env')
const Mail = use('Mail')
const Boat = use('App/Models/Boat')

class getBoatInfo extends Command {
  static get signature () {
    return `
      bbapi:getBoatInfo
      { --boatid=@value : define a singular boat to update}
    `
  }

  static get description () {
    return 'Update boat equipment, extras and images'
  }

  async handle (args, options) {
    await this.getBoatInfo()
    console.log('completed')

    const data = {
      message: 'Finished updating individual boats from Get Boat Info'
    }

    await Mail.send('emails.cron', data, (message) => {
      message
        .to('matt@saowapan.com')
        .from('info@sailchecker.com')
        .subject('Finished Get Boat Info')
    })

    Database.close()
  }

  async getBoatInfo () {
    /*
      Use only the minimum params, to get full listings
    */
    const form = {
      username: Env.get('BB_USERNAME'),
      password: Env.get('BB_PASSWORD'),
      lang: 'en',
      loadEquipment: true,
      loadExtras: true,
      loadAvailability: true,
      stateDate: new Date().toJSON().slice(0, 10).replace(/-/g, '/')
    }

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

    for (let i of BoatIDS) {
      let togo = BoatIDS.length - index
      console.log('to go: ' + togo)
      form.boatid = i.ID
      options.url = 'https://api.boatbooker.net/ws/boatinfo/getboatinfo?' + querystring.stringify(form)

      try {
        const data = await SCHelpers.Request(options)
        const BoatID = data.BoatID

        let boat = await Boat.find(BoatID)
        boat.FleetOperatorID = data.FleetOperatorID
        boat.save()

        // let Bases = data.Bases
        let Equipment = data.Equipment
        let Extras = data.Extras

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

        // if (!Array.isArray(Bases) || !Bases.length === 0) {
        //   let newBases = []

        //   Bases = _.map(Bases, (b) => {
        //     newBases.push({
        //       BoatID: BoatID,
        //       ID: b.ID,
        //       isPrimary: (b.IsPrimary) ? b.IsPrimary : 0
        //     })

        //     // redundant, this exists in the regions table
        //     delete b.Country
        //     delete b.Latitude
        //     delete b.Longitude

        //     b.BoatID = BoatID
        //     return b
        //   })

        //   let currentBases = await Database.select('BoatID', 'ID').from('boat_bases').where({ BoatID: BoatID })

        //   await Database.insertUpdate('boat_bases', Bases)

        //   let diff = _.differenceWith(newBases, currentBases, _.isEqual)

        //   // delete boat bases if they change
        //   if (diff) {
        //     for (let d of diff) {
        //       await Database.table('boat_bases').where(d).del()
        //     }
        //   }
        // }

        if (!Array.isArray(Equipment) || Equipment.length !== 0) {
          let newEquipment = []

          Equipment = _.map(Equipment, (e) => {
            newEquipment.push({
              BoatID: BoatID,
              EquipmentTypeID: e.EquipmentTypeID
            })

            return e
          })

          let currentEquipment = await Database.select('BoatID', 'EquipmentTypeID').from('boat_equipments').where({ BoatID: BoatID })

          await Database.insertUpdate('boat_equipments', Equipment)

          let diff = _.differenceWith(newEquipment, currentEquipment, _.isEqual)

          // delete boat bases if they change
          if (diff) {
            for (let d of diff) {
              await Database.table('boat_equipments').where(d).del()
            }
          }
        }

        if (!Array.isArray(Extras) || Extras.length !== 0) {
          let ExtrasPrices = []
          let ExtrasPricesCharterDates = []

          Extras = _.map(Extras, (e) => {
            e.Prices = _.map(e.Prices, (ep) => {
              if (!ep.CharterDates.length === 0) {
                let charter = {
                  PriceID: ep.ID,
                  DateFrom: ep.CharterDates[0].DateFrom,
                  DateTo: ep.CharterDates[0].DateTo,
                  ExtraID: e.ExtraID
                }
                ExtrasPricesCharterDates.push(charter)
              }

              ep.BoatID = BoatID
              ep.Description = (ep.Description) ? ep.Description : ''

              delete ep.CharterDates
              delete ep.EndingBases
              delete ep.StartingBases
              delete ep.PriceRefundable
              delete ep.TransferPeopleTo
              delete ep.TransferPeopleFrom
              delete ep.TransferStartLocation
              delete ep.TransferEndLocation
              delete ep.MeanOfTransportationID

              ExtrasPrices.push(ep)
              return ep
            })

            delete e.Prices
            delete e.ExcludesExtras
            e.BoatID = BoatID
            e.ServiceID = (e.ServiceID) ? e.ServiceID : false
            e.ServiceTypeID = (e.ServiceTypeID) ? e.ServiceTypeID : false

            return e
          })

          await Database.insertUpdate('boat_extras', Extras)
          if (!Array.isArray(ExtrasPrices) || ExtrasPrices.length !== 0) {
            await Database.insertUpdate('boat_extra_prices', ExtrasPrices)
          }

          if (!Array.isArray(ExtrasPricesCharterDates) || ExtrasPricesCharterDates.length !== 0) {
            await Database.insertUpdate('boat_extra_price_dates', ExtrasPricesCharterDates)
          }
        }
      } catch (e) {
        console.log(`failed on BoatID: ${BoatIDS[index].ID}`)
        console.log(e)
      }

      index++
    }
    return true
  }
}

module.exports = getBoatInfo
