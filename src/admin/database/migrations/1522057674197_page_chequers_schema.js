'use strict'

const Schema = use('Schema')

class PageChequersSchema extends Schema {
  up () {
    this.table('page_chequers', (table) => {
      // alter table
      table.dropColumn('image_id')
      table.string('altText')
      table.string('titleText')
      table.string('fileName')
    })
  }

  down () {
    this.table('page_chequers', (table) => {
      // reverse alternations
      table.integer('image_id')
    })
  }
}

module.exports = PageChequersSchema
