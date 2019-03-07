'use strict'

const Schema = use('Schema')

class PageBoatTypesSchema extends Schema {
  up () {
    this.create('page_boat_types', (table) => {
      table.integer('page_id')
      table.integer('type_id')
      table.unique(['page_id', 'type_id'])
    })
  }

  down () {
    this.drop('page_boat_types')
  }
}

module.exports = PageBoatTypesSchema
