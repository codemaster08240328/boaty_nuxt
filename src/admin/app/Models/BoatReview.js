'use strict'

const Model = use('Model')

class BoatReview extends Model {
  boat () {
    return this.belongsTo('App/Models/Boat')
  }
}

module.exports = BoatReview
