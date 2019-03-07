'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkBoatLocation extends Model {
  base () {
    return this.belongsTo('App/Models/MmkBase', 'id', 'base_id')
  }

  boat () {
    return this.belongsTo('App/Models/MmkBoat', 'id', 'boat_id')
  }

  static scopeDefaultBase (query) {
    return query.where('default_base', true)
  }

  static scopeNotDefaultBase (query) {
    return query.where('default_base', false)
  }
}

module.exports = MmkBoatLocation
