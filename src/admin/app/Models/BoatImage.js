'use strict'

const Model = use('Model')

class BoatImage extends Model {
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
}

module.exports = BoatImage
