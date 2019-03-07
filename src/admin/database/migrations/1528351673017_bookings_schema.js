'use strict'

const Schema = use('Schema')

class BookingsSchema extends Schema {
  up () {
    this.table('bookings', (table) => {
      // alter table
      table.string('gclid')
    })
  }

  down () {
    this.table('bookings', (table) => {
      // reverse alternations
      table.dropColumn('gclid')
    })
  }
}

module.exports = BookingsSchema
