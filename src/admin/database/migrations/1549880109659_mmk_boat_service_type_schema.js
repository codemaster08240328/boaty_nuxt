'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBoatServiceTypeSchema extends Schema {
  up () {
    this.create('mmk_boat_service_types', (table) => {
      table.increments()
      table.string('name')
      table.timestamps()
    })
  }

  down () {
    this.drop('mmk_boat_service_types')
  }
}

module.exports = MmkBoatServiceTypeSchema
