'use strict'

const Schema = use('Schema')

class ContactsSchema extends Schema {
  up () {
    this.table('contacts', (table) => {
      // alter table
      table.integer('boat_id').nullable().alter()
      table.integer('base_id').nullable().alter()
      table.integer('adults').nullable().alter()
      table.integer('children').nullable().alter()
      table.string('phone_number').nullable().alter()
      table.string('ip').nullable().alter()
      table.integer('boat_price_euro').nullable().alter()
      table.text('user_agent').nullable().alter()
    })
  }

  down () {
    this.table('contacts', (table) => {
      // reverse alternations
    })
  }
}

module.exports = ContactsSchema
