'use strict'

const Schema = use('Schema')

class PageTagsSchema extends Schema {
  up () {
    this.table('page_tags', (table) => {
      // alter table
      table.unique(['page_id', 'object_id', 'type'])
    })
  }

  down () {
    this.table('page_tags', (table) => {
      // reverse alternations
      table.dropUnique(['page_id', 'object_id', 'type'])
    })
  }
}

module.exports = PageTagsSchema
