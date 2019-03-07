'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBoatDiscountSchema extends Schema {
  up () {
    this.create('mmk_boat_discounts', (table) => {
      table.increments()
      table.integer('boat_id')
      table.bigint('mmk_id')
      table.string('name')
      table.float('percentage')
      table.date('valid_from')
      table.date('valid_to')
      table.date('sailing_from')
      table.date('sailing_to')
      table.integer('valid_days_min')
      table.integer('valid_days_max')
      table.integer('discount_type')
      table.boolean('included_in_base_price')
      table.boolean('excludes_others')
      table.boolean('affected_by_max_value')
      table.integer('base_id')
      table.timestamps()
      table.index('boat_id')
      table.index('base_id')
    })
  }

  down () {
    this.drop('mmk_boat_discounts')
  }
}

module.exports = MmkBoatDiscountSchema
