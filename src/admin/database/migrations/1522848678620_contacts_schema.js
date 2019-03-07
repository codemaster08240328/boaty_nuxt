'use strict'

const Schema = use('Schema')

class ContactsSchema extends Schema {
  up () {
    this.table('contacts', (table) => {
      // alter table
      table.index('boat_id', 'boatid-index')
      table.index('base_id', 'baseid-index')
      table.integer('user_id').unsigned()
      table.string('browser').alter()
      table.boolean('status').notNullable().alter()
    })
  }

  down () {
    this.table('contacts', (table) => {
      // reverse alternations
      table.dropIndex('boat_id')
      table.dropIndex('base_id')
      table.dropIndex('user_id')
      table.dropColumn('user_id')
      table.integer('browser').alter()
      table.boolean('status').nullable().alter()
    })
  }
}

module.exports = ContactsSchema
