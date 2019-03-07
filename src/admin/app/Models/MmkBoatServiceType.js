'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkBoatServiceType extends Model {
  boats () {
    return this.hasMany('App/Models/MmkBoat', 'id', 'service_type_id')
  }
}

module.exports = MmkBoatServiceType
