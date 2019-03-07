'use strict'

const Schema = use('Schema')

class PageChequersSchema extends Schema {
  up () {
    this.create('page_chequers', (table) => {
      table.increments()
      table.timestamps()
      table.string('title')
      table.text('body')
      table.integer('image_id').unsigned()
      table.integer('order').unsigned()
    })
  }

  down () {
    this.drop('page_chequers')
  }
}

module.exports = PageChequersSchema
