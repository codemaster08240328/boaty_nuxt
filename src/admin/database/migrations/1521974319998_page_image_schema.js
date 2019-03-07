'use strict'

const Schema = use('Schema')

class PageImageSchema extends Schema {
  up () {
    this.table('page_images', (table) => {
      // alter table
      table.string('position')
    })
  }

  down () {
    this.table('page_images', (table) => {
      // reverse alternations
      table.dropColumn('position')
    })
  }
}

module.exports = PageImageSchema
