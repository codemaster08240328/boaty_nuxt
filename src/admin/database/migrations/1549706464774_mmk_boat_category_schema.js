'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBoatCategorySchema extends Schema {
  up () {
    this.create('mmk_boat_categories', (table) => {
      table.increments()
      table.integer('boat_id')
      table.string('name')
      table.date('start_date')
      table.date('end_date')
      table.timestamps()
      table.index('boat_id')
    })
  }

  down () {
    this.drop('mmk_boat_categories')
  }
}

module.exports = MmkBoatCategorySchema
