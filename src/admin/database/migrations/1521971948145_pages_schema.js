'use strict'

const Schema = use('Schema')

class PagesSchema extends Schema {
  up () {
    this.table('pages', (table) => {
      table.renameColumn('page_type', 'type')
      table.string('locale')
      table.string('template')
    })
  }

  down () {
    this.table('pages', (table) => {
      // reverse alternations
      table.renameColumn('type', 'page_type')
      table.dropColumn('locale')
      table.dropColumn('template')
    })
  }
}

module.exports = PagesSchema
