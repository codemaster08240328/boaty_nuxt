'use strict'

const Schema = use('Schema')

class BoatsSchema extends Schema {
  up () {
    this.table('boats', (table) => {
      table.integer('FleetOperatorID')
      // alter table
    })
  }

  down () {
    this.table('boats', (table) => {
      // reverse alternations
      table.dropColumn('FleetOperatorID')
    })
  }
}

module.exports = BoatsSchema
