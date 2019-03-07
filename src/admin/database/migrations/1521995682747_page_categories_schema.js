'use strict'

const Schema = use('Schema')

class PageCategoriesSchema extends Schema {
  up () {
    this.table('page_categories', (table) => {
      // alter table
      table.unique(['page_id', 'category_id'])
    })
  }

  down () {
    this.table('page_categories', (table) => {
      // reverse alternations
      table.dropUnique(['page_id', 'category_id'])
    })
  }
}

module.exports = PageCategoriesSchema
