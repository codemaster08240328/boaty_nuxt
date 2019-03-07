'use strict'

const Schema = use('Schema')

class PageBoatBrandsSchema extends Schema {
  up () {
    this.create('page_boat_brands', (table) => {
      table.integer('page_id')
      table.integer('brand_id')
      table.unique(['page_id', 'brand_id'])
    })
  }

  down () {
    this.drop('page_boat_brands')
  }
}

module.exports = PageBoatBrandsSchema
