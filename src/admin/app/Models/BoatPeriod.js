'use strict'

const Model = use('Model')

class BoatPeriod extends Model {
  static get incrementing () {
    return false
  }
  // this table has a composite key, not supported in adonis lucid 2018 march
  static get primaryKey () {
    return null
  }
  // disable created at
  static get createTimestamp () {
    return null
  }
  static get updateTimestamp () {
    return null
  }
}

module.exports = BoatPeriod
