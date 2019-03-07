'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBoatKindSchema extends Schema {
  up () {
    this.create('mmk_boat_kinds', (table) => {
      table.increments()
      table.string('name')
      table.timestamps()
    })
  }

  down () {
    this.drop('mmk_boat_kinds')
  }
}

module.exports = MmkBoatKindSchema
