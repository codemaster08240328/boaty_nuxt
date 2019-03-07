'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkRegionSchema extends Schema {
  up () {
    this.create('mmk_regions', (table) => {
      table.increments()
      table.integer('mmk_id').unsigned().unique()
      table.string('name')
      table.string('slug')
      table.index('slug')
      table.timestamps()
    })
  }

  down () {
    this.drop('mmk_regions')
  }
}

module.exports = MmkRegionSchema
