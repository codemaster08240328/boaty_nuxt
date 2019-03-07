'use strict'

const Schema = use('Schema')

class PageChequersSchema extends Schema {
  up () {
    this.table('page_chequers', (table) => {
      // alter table
      table.string('title').notNullable().alter()
      table.text('body').notNullable().alter()
      table.integer('page_id').notNullable().alter()
      table.string('altText').notNullable().alter()
      table.string('titleText').notNullable().alter()
      table.string('fileName').notNullable().alter()
      table.index('page_id')
    })
  }

  down () {
    this.table('page_chequers', (table) => {
      // reverse alternations
      table.string('title').nullable().alter()
      table.text('body').nullable().alter()
      table.integer('page_id').nullable().alter()
      table.string('altText').nullable().alter()
      table.string('titleText').nullable().alter()
      table.string('fileName').nullable().alter()
      table.dropIndex('page_id')
    })
  }
}

module.exports = PageChequersSchema
