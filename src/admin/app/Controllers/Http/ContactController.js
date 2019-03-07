'use strict'
const geoip = require('geoip-lite')
const Contact = use('App/Models/Contact')
const Mail = use('Mail')
const Database = use('Database')
const Boat = use('App/Models/Boat')
const Url = use('App/Services/Url')
const uniqid = require('uniqid')
const _ = use('lodash')

class ContactController {
  async update ({ request, params }) {
    const data = request.all()
    const contact = await Contact.findBy('confirm_code', params.confirm_code)

    _.merge(contact, data)

    await contact.save()

    return {
      contact: contact,
      data: data,
      confirm: params.confirm_code
    }
  }

  async store ({ request }) {
    let contact = request.all()

    contact.ip = request.request.connection.remoteAddress
    contact.user_agent = request.request.headers['user-agent']

    const geo = geoip.lookup(contact.ip) // get geo ip data

    if (geo != null) {
      contact.country = geo.country
      contact.region = geo.region
      contact.city = geo.city
    }

    contact.confirm_code = uniqid()
    contact.status = 1

    const saved = await Contact.create(contact)

    if (contact.base_id && contact.boat_id) {
      const region = await Database.from('regions').where('base_id', contact.base_id).first()
      const boat = await Boat.find(contact.boat_id)

      contact.boat = boat
      contact.boat.link = '/boat' + Url.stringToUrl({ urlParts: [ boat.BrandName, boat.ModelName + '-' + boat.ID ] })
      contact.location = `${region.country_name_en}, ${region.base_name}`
      contact.country = region.country_name_en

      // TODO: add these back in using V3 content
      // contact.sailing_itinerary = await Redis.get(`wp-sailing-itinerary-${region.country_name_en}`.toLowerCase())
      // contact.sailing_itinerary = JSON.parse(contact.sailing_itinerary)
    }

    if (!contact.phone_number && process.env.NODE_ENV === 'production') {
      await Mail.send('emails.contact', contact, (message) => {
        message
          .to(contact.email)
          .bcc('info@sailchecker.com')
          .from('info@sailchecker.com')
          .subject(`Confirmation of SailChecker Yacht Request for ${contact.name}`)
      })
    }

    return saved
  }
}

module.exports = ContactController
