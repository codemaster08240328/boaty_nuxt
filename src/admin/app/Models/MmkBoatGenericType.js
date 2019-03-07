'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkBoatGenericType extends Model {
  boats () {
    return this.hasMany('App/Models/MmkBoat', 'id', 'generic_type_id')
  }
}

module.exports = MmkBoatGenericType
