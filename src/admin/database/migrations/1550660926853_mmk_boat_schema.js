'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBoatSchema extends Schema {
  up () {
    this.table('mmk_boats', (table) => {
      table.float('saturday_price')
      table.integer('saturday_base_id')
      table.text('availability')
      table.index('saturday_base_id')
    })
  }

  down () {
    this.table('mmk_boats', (table) => {
      table.dropIndex('saturday_base_id')
      table.dropColumn('availability')
      table.dropColumn('saturday_base_id')
      table.dropColumn('saturday_price')
    })
  }
}

module.exports = MmkBoatSchema
