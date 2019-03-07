'use strict'

const Model = use('Model')

class Country extends Model {
  static get primaryKey () {
    return 'ID'
  }
}

module.exports = Country
