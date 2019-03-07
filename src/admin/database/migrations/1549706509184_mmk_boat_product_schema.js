'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBoatProductSchema extends Schema {
  up () {
    this.create('mmk_boat_products', (table) => {
      table.increments()
      table.integer('boat_id')
      table.string('name')
      table.timestamps()
      table.index('boat_id')
    })
  }

  down () {
    this.drop('mmk_boat_products')
  }
}

module.exports = MmkBoatProductSchema
