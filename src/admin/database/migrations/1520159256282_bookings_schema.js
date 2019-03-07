'use strict'

const Schema = use('Schema')

class BookingsSchema extends Schema {
  up () {
    this.table('bookings', (table) => {
      // alter table
      table.boolean('status')
    })
  }

  down () {
    this.table('bookings', (table) => {
      table.dropColumn('status')
    })
  }
}

module.exports = BookingsSchema
