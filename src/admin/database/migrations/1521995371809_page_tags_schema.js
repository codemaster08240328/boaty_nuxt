'use strict'

const Schema = use('Schema')

class PageTagsSchema extends Schema {
  up () {
    this.table('page_tags', (table) => {
      // alter table
      table.dropColumn('tag_id')
      table.integer('object_id')
      table.string('type')
    })
  }

  down () {
    this.table('page_tags', (table) => {
      // reverse alternations
      table.integer('tag_id')
      table.dropColumn('object_id')
      table.dropColumn('type')
    })
  }
}

module.exports = PageTagsSchema
