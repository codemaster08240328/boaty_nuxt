'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBoatPriceSchema extends Schema {
  up () {
    this.create('mmk_boat_prices', (table) => {
      table.increments()
      table.integer('boat_id')
      table.date('date_from')
      table.date('date_to')
      table.float('price')
      table.string('currency')
      table.timestamps()
      table.index('boat_id')
    })
  }

  down () {
    this.drop('mmk_boat_prices')
  }
}

module.exports = MmkBoatPriceSchema
