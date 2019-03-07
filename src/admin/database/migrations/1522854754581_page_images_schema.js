'use strict'

const Schema = use('Schema')

class PageImagesSchema extends Schema {
  up () {
    this.table('page_images', (table) => {
      // alter table
      table.index('page_id')
    })
  }

  down () {
    this.table('page_images', (table) => {
      // reverse alternations
      table.dropIndex('page_id')
    })
  }
}

module.exports = PageImagesSchema
