'use strict'

const Model = use('Model')

class BoatBase extends Model {
  static get primaryKey () {
    return 'BoatID'
  }
}

module.exports = BoatBase
