'use strict'

const Schema = use('Schema')

class PagesSchema extends Schema {
  up () {
    this.table('pages', (table) => {
      // alter table
      table.integer('order').unsigned()
    })
  }

  down () {
    this.table('pages', (table) => {
      // reverse alternations
      table.dropColumn('order')
    })
  }
}

module.exports = PagesSchema
