'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBoatExtraSchema extends Schema {
  up () {
    this.create('mmk_boat_extras', (table) => {
      table.increments()
      table.integer('boat_id')
      table.bigint('mmk_id')
      table.string('name')
      table.float('price')
      table.string('time_unit')
      table.string('currency')
      table.boolean('per_person')
      table.date('valid_from')
      table.date('valid_to')
      table.date('sailing_from')
      table.date('sailing_to')
      table.boolean('obligatory')
      table.boolean('included_in_base_price')
      table.boolean('payable_on_invoice')
      table.integer('base_id')
      table.boolean('deposit_waiver')
      table.timestamps()
      table.index('boat_id')
      table.index('base_id')
    })
  }

  down () {
    this.drop('mmk_boat_extras')
  }
}

module.exports = MmkBoatExtraSchema
