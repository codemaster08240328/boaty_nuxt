'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkBoatImageSchema extends Schema {
  up () {
    this.create('mmk_boat_images', (table) => {
      table.increments()
      table.integer('boat_id')
      table.string('href')
      table.string('comment')
      table.timestamps()
      table.index('boat_id')
    })
  }

  down () {
    this.drop('mmk_boat_images')
  }
}

module.exports = MmkBoatImageSchema
