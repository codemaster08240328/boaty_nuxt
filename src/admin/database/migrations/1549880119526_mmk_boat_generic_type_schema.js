'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBoatGenericTypeSchema extends Schema {
  up () {
    this.create('mmk_boat_generic_types', (table) => {
      table.increments()
      table.string('name')
      table.timestamps()
    })
  }

  down () {
    this.drop('mmk_boat_generic_types')
  }
}

module.exports = MmkBoatGenericTypeSchema
