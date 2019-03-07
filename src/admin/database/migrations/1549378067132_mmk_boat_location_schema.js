'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBoatLocationSchema extends Schema {
  up () {
    this.create('mmk_boat_locations', (table) => {
      table.increments()
      table.integer('boat_id')
      table.integer('base_id')
      table.date('date_from')
      table.date('date_to')
      table.boolean('default_base')
      table.timestamps()
      table.index('boat_id')
      table.index('base_id')
    })
  }

  down () {
    this.drop('mmk_boat_locations')
  }
}

module.exports = MmkBoatLocationSchema
