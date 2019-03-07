'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkCountrySchema extends Schema {
  up () {
    this.create('mmk_countries', (table) => {
      table.increments()
      table.integer('mmk_id').unique()
      table.string('name')
      table.string('slug')
      table.string('alpha2')
      table.string('alpha3')
      table.integer('region_id')
      table.timestamps()
      table.index('region_id')
      table.index('slug')
    })
  }

  down () {
    this.drop('mmk_countries')
  }
}

module.exports = MmkCountrySchema
