'use strict'

const Schema = use('Schema')

class PageCategoriesSchema extends Schema {
  up () {
    this.create('page_categories', (table) => {
      table.increments()
      table.timestamps()
      table.integer('page_id')
      table.integer('category_id')
    })
  }

  down () {
    this.drop('page_categories')
  }
}

module.exports = PageCategoriesSchema
