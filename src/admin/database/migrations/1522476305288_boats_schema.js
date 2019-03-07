'use strict'

const Schema = use('Schema')

class BoatsSchema extends Schema {
  up () {
    this.table('boats', (table) => {
      // alter table
      table.decimal('rating', 2, 1)
      table.index('rating', 'rating_index')
    })
  }

  down () {
    this.table('boats', (table) => {
      // reverse alternations
      table.dropColumn('rating')
    })
  }
}

module.exports = BoatsSchema
