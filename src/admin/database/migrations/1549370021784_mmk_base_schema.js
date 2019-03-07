'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBaseSchema extends Schema {
  up () {
    this.create('mmk_bases', (table) => {
      table.increments()
      table.bigint('mmk_id').unsigned().unique()
      table.string('name')
      table.string('slug')
      table.string('city')
      table.integer('city_id')
      table.integer('country_id')
      table.string('country_alpha2')
      table.string('address')
      table.float('longitude')
      table.float('latitude')
      table.timestamps()
      table.index('country_id')
      table.index('slug')
      table.index('city_id')
    })
  }

  down () {
    this.drop('mmk_bases')
  }
}

module.exports = MmkBaseSchema
