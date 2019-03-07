'use strict'

const Schema = use('Schema')

class PagesSchema extends Schema {
  up () {
    this.table('pages', (table) => {
      // alter table
      table.string('pdf_guide')
    })
  }

  down () {
    this.table('pages', (table) => {
      // reverse alternations
      table.dropColumn('pdf_guide')
    })
  }
}

module.exports = PagesSchema
