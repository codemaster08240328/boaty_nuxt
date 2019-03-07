'use strict'

const Schema = use('Schema')

class ContactsSchema extends Schema {
  up () {
    this.table('contacts', (table) => {
      // alter table
      table.string('gclid')
    })
  }

  down () {
    this.table('contacts', (table) => {
      // reverse alternations
      table.dropColumn('gclid')
    })
  }
}

module.exports = ContactsSchema
