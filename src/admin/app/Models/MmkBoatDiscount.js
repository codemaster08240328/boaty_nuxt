'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkBoatDiscount extends Model {
  base () {
    return this.belongsTo('App/Models/MmkBase', 'id', 'base_id')
  }

  boat () {
    return this.belongsTo('App/Models/MmkBoat', 'id', 'boat_id')
  }
}

module.exports = MmkBoatDiscount
