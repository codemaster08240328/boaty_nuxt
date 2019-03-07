'use strict'

const Model = use('Model')

class Boat extends Model {
  static get primaryKey () {
    return 'ID'
  }

  boatImage () {
    return this.hasMany('App/Models/BoatImage')
  }
  boatModelImage () {
    return this.hasMany('App/Models/BoatModelImage')
  }
  boatBase () {
    return this.hasMany('App/Models/BoatBase')
  }
  boatPeriod () {
    return this.hasMany('App/Models/BoatPeriod')
  }
  boatReview () {
    return this.hasMany('App/Models/BoatReview')
  }
}

module.exports = Boat
