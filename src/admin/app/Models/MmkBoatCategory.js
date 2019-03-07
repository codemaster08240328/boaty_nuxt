'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkBoatCategory extends Model {
  boat () {
    return this.belongsTo('App/Models/MmkBoat', 'id', 'boat_id')
  }
}

module.exports = MmkBoatCategory
