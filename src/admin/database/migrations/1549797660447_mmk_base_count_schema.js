'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBaseCountSchema extends Schema {
  up () {
    this.create('mmk_base_counts', (table) => {
      table.increments()
      table.integer('base_id')
      table.date('calculation_date')
      table.integer('boats_count')
      table.float('min_price')
      table.timestamps()
      table.index('base_id')
    })
  }

  down () {
    this.drop('mmk_base_counts')
  }
}

module.exports = MmkBaseCountSchema
