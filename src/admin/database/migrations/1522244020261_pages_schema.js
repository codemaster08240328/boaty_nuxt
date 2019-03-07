'use strict'

const Schema = use('Schema')

class PagesSchema extends Schema {
  up () {
    this.table('pages', (table) => {
      // alter table
      table.dropColumn('location_id')
      table.dropColumn('location_type')
      table.integer('country_id')
      table.integer('area_id')
      table.integer('base_id')
    })
  }

  down () {
    this.table('pages', (table) => {
      // reverse alternations
      table.integer('location_id')
      table.string('location_type')
      table.dropColumn('country_id')
      table.dropColumn('area_id')
      table.dropColumn('base_id')
    })
  }
}

module.exports = PagesSchema
