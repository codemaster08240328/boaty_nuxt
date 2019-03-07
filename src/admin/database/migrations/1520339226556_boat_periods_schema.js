'use strict'

const Schema = use('Schema')

class BoatPeriodsSchema extends Schema {
  up () {
    this.table('boat_periods', (table) => {
      // alter table
      table.timestamp('created_at').defaultTo(this.fn.now())
      table.timestamp('updated_at')
    })
  }

  down () {
    this.table('boat_periods', (table) => {
      // reverse alternations
      table.dropColumn('created_at')
      table.dropColumn('updated_at')
    })
  }
}

module.exports = BoatPeriodsSchema
