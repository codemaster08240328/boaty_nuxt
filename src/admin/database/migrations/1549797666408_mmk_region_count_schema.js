'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkRegionCountSchema extends Schema {
  up () {
    this.create('mmk_region_counts', (table) => {
      table.increments()
      table.integer('region_id')
      table.float('lat')
      table.float('lng')
      table.integer('boats_count')
      table.float('min_price')
      table.timestamps()
      table.index('region_id')
    })
  }

  down () {
    this.drop('mmk_region_counts')
  }
}

module.exports = MmkRegionCountSchema
