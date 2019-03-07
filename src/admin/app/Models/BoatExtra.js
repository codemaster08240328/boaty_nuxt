'use strict'

const Model = use('Model')

class BoatExtra extends Model {
  // disable created at
  static get createTimestamp () {
    return null
  }
  static get updateTimestamp () {
    return null
  }

  static get primaryKey () {
    return 'BoatID'
  }

  BoatExtraPrice () {
    return this.hasMany('App/Models/BoatExtraPrice', 'ExtraID')
  }
}

module.exports = BoatExtra
