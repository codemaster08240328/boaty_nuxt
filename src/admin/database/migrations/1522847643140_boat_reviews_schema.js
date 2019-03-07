'use strict'

const Schema = use('Schema')

class BoatReviewsSchema extends Schema {
  up () {
    this.table('boat_reviews', (table) => {
      // alter table
      table.boolean('status').notNullable().defaultTo('0').alter()
      table.index('BoatID', 'boatid-index')
    })
  }

  down () {
    this.table('boat_reviews', (table) => {
      // reverse alternations
      table.dropColumn('status')
      table.dropIndex('BoatID', 'boatid-index')
      table.integer('status')
    })
  }
}

module.exports = BoatReviewsSchema
