'use strict'

const Schema = use('Schema')

class BoatsSchema extends Schema {
  up () {
    this.table('boats', (table) => {
      // alter table
      table.decimal('rating', 3, 1).alter()
    })
  }

  down () {
    this.table('boats', (table) => {
      // reverse alternations
      table.decimal('rating', 2, 1).alter()
    })
  }
}

module.exports = BoatsSchema
