'use strict'

const Model = use('Model')

class Base extends Model {
  // disable created at
  static get createTimestamp () {
    return null
  }
  static get updateTimestamp () {
    return null
  }

  static get primaryKey () {
    return 'ID'
  }

  region () {
    return this.belongsTo('App/Models/Region')
  }
}

module.exports = Base
