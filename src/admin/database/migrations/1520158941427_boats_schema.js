'use strict'

const Schema = use('Schema')

class BoatsSchema extends Schema {
  up () {
    this.table('boats', (table) => {
      // alter table
      table.boolean('status')
    })
  }

  down () {
    this.table('boats', (table) => {
      table.dropColumn('status')
    })
  }
}

module.exports = BoatsSchema
