'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkBoatKind extends Model {
  boats () {
    return this.hasMany('App/Models/MmkBoat', 'id', 'kind_id')
  }
}

module.exports = MmkBoatKind
