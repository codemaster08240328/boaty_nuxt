'use strict'

const Schema = use('Schema')

class PageChequersSchema extends Schema {
  up () {
    this.table('page_chequers', (table) => {
      // alter table
      table.integer('page_id').unsigned()
    })
  }

  down () {
    this.table('page_chequers', (table) => {
      // reverse alternations
      table.dropColumn('page_id')
    })
  }
}

module.exports = PageChequersSchema
