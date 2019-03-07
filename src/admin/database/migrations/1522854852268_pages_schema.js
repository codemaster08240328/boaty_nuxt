'use strict'

const Schema = use('Schema')

class PagesSchema extends Schema {
  up () {
    this.table('pages', (table) => {
      // alter table
      table.integer('user_id').unsigned().notNullable()
    })
  }

  down () {
    this.table('pages', (table) => {
      // reverse alternations
      table.dropColumn('user_id')
    })
  }
}

module.exports = PagesSchema
