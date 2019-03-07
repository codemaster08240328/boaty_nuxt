'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBoatTypeSchema extends Schema {
  up () {
    this.create('mmk_boat_types', (table) => {
      table.increments()
      table.string('name')
      table.timestamps()
    })
  }

  down () {
    this.drop('mmk_boat_types')
  }
}

module.exports = MmkBoatTypeSchema
