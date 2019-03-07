'use strict'

const { Command } = require('@adonisjs/ace')
// const SCHelpers = use('App/Services/Helpers')
// const Database = use('Database')
// const querystring = require('querystring')
const _ = use('lodash')
// const Env = use('Env')
// const Mail = use('Mail')
const fs = require('fs')
const parse = require('csv-parse')
const Database = use('Database')
const Boat = use('App/Models/Boat')

class ClientDatum extends Command {
  static get signature () {
    return 'bbapi:clientData'
  }

  static get description () {
    return 'Gets Booking/Client data from BB api, used in search results and boat ranking'
  }

  async handle (args) {
    let csvData = []
    fs.createReadStream('./Bookings.csv')
      .pipe(parse({delimiter: ','}))
      .on('data', (csvrow) => {
        csvData.push(csvrow[11]) // FleetOperatorID
      })

    const BoatIDS = await Database.select('ID').from('boats').where('status', 1)

    for (const i of BoatIDS) {
      let boat = await Boat.find(i.ID)

      let rating = 7 // base rating
      _.each(csvData, (fo) => {
        if (Number(fo) === boat.FleetOperatorID) {
          rating += 0.3 // each booking
        }
      })

      const d = new Date()
      const year = d.getFullYear()

      // if the boat age is less than 2 years, deduct nothing
      const ageRating = (year < boat.BuildYear + 2) ? 0 : ((year - boat.BuildYear) * 0.2)

      // allow rating to be more than 10 and less than 6.9 - on the frontend clean up and use for ordering backend
      rating = rating - ageRating

      boat.rating = rating.toFixed(1)
      await boat.save()
    }
    Database.close()
  }
}

module.exports = ClientDatum
