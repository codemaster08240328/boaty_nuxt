'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkProductPriceSchema extends Schema {
  up () {
    this.create('mmk_product_prices', (table) => {
      table.increments()
      table.integer('product_id')
      table.date('date_from')
      table.date('date_to')
      table.float('price')
      table.string('currency')
      table.timestamps()
      table.index('product_id')
    })
  }

  down () {
    this.drop('mmk_product_prices')
  }
}

module.exports = MmkProductPriceSchema
