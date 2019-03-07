'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkEquipmentCategorySchema extends Schema {
  up () {
    this.create('mmk_equipment_categories', (table) => {
      table.increments()
      table.integer('mmk_id').unsigned().unique()
      table.string('name')
      table.timestamps()
    })
  }

  down () {
    this.drop('mmk_equipment_categories')
  }
}

module.exports = MmkEquipmentCategorySchema
