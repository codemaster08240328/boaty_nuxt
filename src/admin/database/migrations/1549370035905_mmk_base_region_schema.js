'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBaseRegionSchema extends Schema {
  up () {
    this.create('mmk_base_regions', (table) => {
      table.increments()
      table.integer('mmk_base_id')
      table.integer('mmk_region_id')
      table.timestamps()
      table.unique(['mmk_base_id', 'mmk_region_id'])
    })
  }

  down () {
    this.drop('mmk_base_regions')
  }
}

module.exports = MmkBaseRegionSchema
