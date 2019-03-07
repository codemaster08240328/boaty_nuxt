'use strict'
const Database = use('Database')
const Booking = use('App/Models/Booking')
const _ = use('lodash')
const Url = use('App/Services/Url')

class BookingController {
  async index () {
    let result = await Database.raw(`
    SELECT 
      bk.*, b.BrandName, b.ModelName, bb.Name
    FROM
      bookings bk
          LEFT JOIN
      boats b ON bk.boat_id = b.ID
          LEFT JOIN
      boat_bases bb ON bk.boat_id = bb.BoatID
    WHERE
      bk.status = 1
    `)

    let bookings = result[0]

    _.each(bookings, (value, index) => {
      let boatPrefix = '/boat' // make dynamic
      bookings[index].url = boatPrefix + Url.stringToUrl({ urlParts: [ value.BrandName, value.ModelName + '-' + value.boat_id ] })
    })

    return bookings
  }

  async delete ({ params }) {
    const { id } = params
    const booking = await Booking.find(id)
    booking.status = 0
    return booking.save()
  }
}

module.exports = BookingController
