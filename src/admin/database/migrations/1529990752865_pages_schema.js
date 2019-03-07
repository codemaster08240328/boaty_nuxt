'use strict'

const Schema = use('Schema')

class PagesSchema extends Schema {
  up () {
    this.table('pages', (table) => {
      // alter table
      table.string('subtitle')
      table.string('keyword')
      table.boolean('status')
    })
  }

  down () {
    this.table('pages', (table) => {
      // reverse alternations
      table.dropColumns('subtitle', 'keyword', 'status')
    })
  }
}

module.exports = PagesSchema
