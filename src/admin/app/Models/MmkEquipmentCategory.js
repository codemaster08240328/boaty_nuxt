'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkEquipmentCategory extends Model {
  equipments () {
    return this.hasMany('App/Models/MmkBoatEquipment', 'id', 'category_id')
  }
}

module.exports = MmkEquipmentCategory
