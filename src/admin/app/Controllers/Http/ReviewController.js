'use strict'
const BoatReview = use('App/Models/BoatReview')
const geoip = require('geoip-lite')
const Mail = use('Mail')

class ReviewController {
  async store ({ request }) {
    let review = request.all()

    review.ip = request.request.connection.remoteAddress
    review.user_agent = request.request.headers['user-agent']

    const geo = geoip.lookup(review.ip) // get geo ip data

    if (geo != null) {
      review.country = geo.country
      review.region = geo.region
      review.city = geo.city
    }

    const result = await BoatReview.create(review)

    await Mail.send('emails.review', review, (message) => {
      message
        .to(review.email)
        .from('info@sailchecker.com')
        .subject(`Confirmation of SailChecker Review for ${review.name}`)
    })

    return result
  }
}

module.exports = ReviewController
