'use strict'

const Schema = use('Schema')

class PageImagesSchema extends Schema {
  up () {
    this.table('page_images', (table) => {
      // alter table
      table.dropColumn('page_chequer_id')
    })
  }

  down () {
    this.table('page_images', (table) => {
      // reverse alternations
      table.integer('page_chequer_id')
    })
  }
}

module.exports = PageImagesSchema
