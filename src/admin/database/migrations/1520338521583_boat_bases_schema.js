'use strict'

const Schema = use('Schema')

class BoatBasesSchema extends Schema {
  up () {
    this.table('boat_bases', (table) => {
      // alter table
      table.timestamp('created_at').defaultTo(this.fn.now())
      table.timestamp('updated_at')
    })
  }

  down () {
    this.table('boat_bases', (table) => {
      // reverse alternations
      table.dropColumn('created_at')
      table.dropColumn('updated_at')
    })
  }
}

module.exports = BoatBasesSchema
