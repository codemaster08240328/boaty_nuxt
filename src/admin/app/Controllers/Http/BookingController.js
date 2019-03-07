'use strict'
const geoip = require('geoip-lite')
const Booking = use('App/Models/Booking')
const Mail = use('Mail')
const Database = use('Database')
const Url = use('App/Services/Url')
const Boat = use('App/Models/Boat')
const Redis = use('Redis')
const Env = use('Env')
const SCHelpers = use('App/Services/Helpers')
const moment = use('moment')
class BookingController {
  async store ({ request }) {
    let booking = request.all()

    /** 
     *    https://api.boatbooker.net/SearchEngineAPI/Search/PlaceBoatOrder 
     */
    const form = {
      username: Env.get('BB_USERNAME'),
      password: Env.get('BB_PASSWORD'),
      pid: Env.get('BB_PID'),
      ProceedToOnlinePayment: false,
      SendClientOrderEmail: false,
      ContactData: {
        FirstName: 'Test', // booking.name.split(' ')[0],
        LastName: 'Test', // booking.name.split(' ')[1],
        Email: booking.email,
        FixedPhoneNumber: booking.phone_number,
        Notes: booking.message
      },
      SelectedBoats: [
        {
          BoatID: booking.boat_id,
          DateFrom: booking.date,
          DateTo: moment(booking.date).add(booking.weeks, 'weeks').format('YYYY-MM-DD'),
          BaseFromID: booking.base_id,
          BaseToID: booking.base_id
        }
      ]
    }

    const bb = {
      url: 'https://api.boatbooker.net/SearchEngineAPI/Search/PlaceBoatOrder',
      headers: {
        'content-type': 'application/x-www-form-urlencoded',
        'Accept-Encoding': 'gzip,deflate'
      },
      gzip: true,
      timeout: 60 * 30 * 1000,
      method: 'post',
      form: form
    }

    booking.adults = parseInt(booking.adults)
    booking.children = parseInt(booking.children)

    booking.ip = request.ip()
    booking.user_agent = request.request.headers['user-agent']

    const geo = geoip.lookup(booking.ip) // get geo ip data

    if (geo != null) {
      booking.country = geo.country
      booking.region = geo.region
      booking.city = geo.city
    }

    booking.confirm_code = 'fake'
    booking.status = 1
    const result = await Booking.create(booking)

    const region = await Database.from('regions').where('base_id', booking.base_id).first()
    const boat = await Boat.find(booking.boat_id)

    booking.boat = boat
    booking.boat.link = '/boat' + Url.stringToUrl({ urlParts: [ boat.BrandName, boat.ModelName + '-' + boat.ID ] })
    booking.location = `${region.country_name_en}, ${region.base_name}`
    booking.country = region.country_name_en

    // TODO: add these back in using V3 content
    // booking.sailing_itinerary = await Redis.get(`wp-sailing-itinerary-${region.country_name_en}`.toLowerCase())
    // booking.sailing_itinerary = JSON.parse(booking.sailing_itinerary)

    if (process.env.NODE_ENV === 'production') {
      await Mail.send('emails.booking', booking, (message) => {
        message
          .to(booking.email)
          .bcc('info@sailchecker.com')
          .from('info@sailchecker.com')
          .subject(`Confirmation of SailChecker Yacht Reservation for ${booking.name}`)
      })

      await SCHelpers.postRequest(bb)
    }

    return result
  }
}

module.exports = BookingController
