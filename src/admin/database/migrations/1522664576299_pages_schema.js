'use strict'

const Schema = use('Schema')

class PagesSchema extends Schema {
  up () {
    this.table('pages', (table) => {
      // alter table
      table.string('slug').index('slug-index')
    })
  }

  down () {
    this.table('pages', (table) => {
      // reverse alternations
      table.dropColumn('slug')
    })
  }
}

module.exports = PagesSchema
