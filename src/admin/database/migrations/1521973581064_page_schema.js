'use strict'

const Schema = use('Schema')

class PageSchema extends Schema {
  up () {
    this.table('pages', (table) => {
      // alter table
      table.string('type').alter()
    })
  }

  down () {
    this.table('pages', (table) => {
      // reverse alternations
      table.dropColumn('type')
    })
  }
}

module.exports = PageSchema
