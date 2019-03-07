'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkCitySchema extends Schema {
  up () {
    this.create('mmk_cities', (table) => {
      table.increments()
      table.integer('country_id')
      table.string('name')
      table.string('slug')
      table.timestamps()
      table.index('country_id')
    })
  }

  down () {
    this.drop('mmk_cities')
  }
}

module.exports = MmkCitySchema
