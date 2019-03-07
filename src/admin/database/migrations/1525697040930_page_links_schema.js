'use strict'

const Schema = use('Schema')

class PageLinksSchema extends Schema {
  up () {
    this.create('page_links', (table) => {
      table.increments()
      table.timestamps()
      table.integer('page_id').unsigned().notNullable()
      table.integer('group_id').unsigned().notNullable()
      table.integer('user_id').unsigned().notNullable()
      table.integer('order').unsigned().notNullable()
      table.string('title').notNullable()
      table.unique(['page_id', 'group_id'], 'unique_page_group')
      table.index('page_id')
      table.index('group_id')
    })
  }

  down () {
    this.drop('page_links')
  }
}

module.exports = PageLinksSchema
