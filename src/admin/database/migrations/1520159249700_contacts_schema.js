'use strict'

const Schema = use('Schema')

class ContactsSchema extends Schema {
  up () {
    this.table('contacts', (table) => {
      // alter table
      table.boolean('status')
    })
  }

  down () {
    this.table('contacts', (table) => {
      table.dropColumn('status')
    })
  }
}

module.exports = ContactsSchema
