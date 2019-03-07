'use strict'

const Schema = use('Schema')

class PageImagesSchema extends Schema {
  up () {
    this.table('page_images', (table) => {
      // alter table
      table.increments()
    })
  }

  down () {
    this.table('page_images', (table) => {
      // reverse alternations
      table.dropColumn('id')
    })
  }
}

module.exports = PageImagesSchema
