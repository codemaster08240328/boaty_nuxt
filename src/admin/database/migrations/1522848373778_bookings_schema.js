'use strict'

const Schema = use('Schema')

class BookingsSchema extends Schema {
  up () {
    this.table('bookings', (table) => {
      // alter table
      table.index('boat_id', 'boatid-index')
      table.index('base_id', 'baseid-index')
      table.index('user_id', 'userid-index')
      table.string('browser').alter()
      table.boolean('status').notNullable().alter()
    })
  }

  down () {
    this.table('bookings', (table) => {
      // reverse alternations
      table.dropIndex('boat_id')
      table.dropIndex('base_id')
      table.dropIndex('user_id')
      table.integer('browser').alter()
      table.boolean('status').nullable().alter()
    })
  }
}

module.exports = BookingsSchema
