'use strict'
const Country = use('App/Models/Country')

class DestinationsController {
  async updatePopularCountry ({ request }) {
    const body = request.only(['ID', 'Popular'])
    const country = await Country.find(body.ID)
    country.Popular = body.Popular
    return country.save()
  }
}

module.exports = DestinationsController
