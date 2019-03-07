'use strict'

const Schema = use('Schema')

class PageCategoriesSchema extends Schema {
  up () {
    this.table('page_categories', (table) => {
      // alter table
      table.dropColumns(['id', 'created_at', 'updated_at'])
      table.integer('category_id').alter().unsigned()
    })
  }

  down () {
    this.table('page_categories', (table) => {
      // reverse alternations
      table.increments()
      table.timestamps()
      table.integer('category_id').alter()
    })
  }
}

module.exports = PageCategoriesSchema
