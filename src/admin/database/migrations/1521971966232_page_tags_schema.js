'use strict'

const Schema = use('Schema')

class PageTagsSchema extends Schema {
  up () {
    this.create('page_tags', (table) => {
      table.increments()
      table.timestamps()
      table.integer('page_id')
      table.integer('tag_id')
    })
  }

  down () {
    this.drop('page_tags')
  }
}

module.exports = PageTagsSchema
