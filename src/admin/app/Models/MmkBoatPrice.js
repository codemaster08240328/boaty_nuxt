'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkBoatPrice extends Model {
  boat () {
    return this.belongsTo('App/Models/MmkBoatPrice', 'id', 'boat_id')
  }
}

module.exports = MmkBoatPrice
