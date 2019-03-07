'use strict'
const Database = use('Database')
const BoatReview = use('App/Models/BoatReview')
const UrlService = use('App/Services/Url')

class ReviewController {
  async index () {
    let boatReviews = await Database.raw(`
    SELECT 
        *
    FROM
        boat_reviews br
    LEFT JOIN
        boats b ON br.BoatID = b.ID`)

    boatReviews[0].map((o) => {
      let boatPrefix = '/boat' // make dynamic
      if (o.BoatID) {
        o.url = boatPrefix + UrlService.stringToUrl({ urlParts: [ o.BrandName, o.ModelName + '-' + o.BoatID ] })
      }
      return o
    })

    return boatReviews[0]
  }

  async update ({ request }) {
    let review = request.all()

    let boatReview = await BoatReview.find(review.id)

    boatReview.status = review.status

    return boatReview.save()
  }
}

module.exports = ReviewController
