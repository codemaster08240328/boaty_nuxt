'use strict'

const Schema = use('Schema')

class BoatsSchema extends Schema {
  up () {
    this.table('boats', (table) => {
      // alter table
      table.timestamp('created_at').defaultTo(this.fn.now())
      table.timestamp('updated_at')
    })
  }

  down () {
    this.table('boats', (table) => {
      // reverse alternations
      table.dropColumn('created_at')
      table.dropColumn('updated_at')
    })
  }
}

module.exports = BoatsSchema
