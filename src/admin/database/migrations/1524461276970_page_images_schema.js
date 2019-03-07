'use strict'

const Schema = use('Schema')

class PageImagesSchema extends Schema {
  up () {
    this.table('page_images', (table) => {
      // alter table
      table.boolean('position').alter()
    })
  }

  down () {
    this.table('page_images', (table) => {
      // reverse alternations
      table.string('position').alter()
    })
  }
}

module.exports = PageImagesSchema
