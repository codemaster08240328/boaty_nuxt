'use strict'

const Schema = use('Schema')

class PageBoatBasesSchema extends Schema {
  up () {
    this.create('page_boat_bases', (table) => {
      table.integer('page_id')
      table.integer('base_id')
      table.unique(['page_id', 'base_id'])
    })
  }

  down () {
    this.drop('page_boat_bases')
  }
}

module.exports = PageBoatBasesSchema
