'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBoatEquipmentSchema extends Schema {
  up () {
    this.create('mmk_boat_equipments', (table) => {
      table.increments()
      table.integer('boat_id')
      table.bigint('mmk_id')
      table.integer('category_id')
      table.string('name')
      table.string('category_name')
      table.string('value')
      table.timestamps()
      table.index('boat_id')
      table.index('category_id')
    })
  }

  down () {
    this.drop('mmk_boat_equipments')
  }
}

module.exports = MmkBoatEquipmentSchema
