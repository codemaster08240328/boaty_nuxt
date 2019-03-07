'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkBoatEquipment extends Model {
  boat () {
    return this.belongsTo('App/Models/MmkBoat', 'id', 'boat_id')
  }

  category () {
    return this.belongsTo('App/Models/MmkEquipmentCategory', 'id', 'category_id')
  }
}

module.exports = MmkBoatEquipment
