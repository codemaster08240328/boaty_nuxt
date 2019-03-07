'use strict'

const Schema = use('Schema')

class BoatImagesSchema extends Schema {
  up () {
    this.table('boat_images', (table) => {
      // alter table
      table.integer('DocumentID').unsigned()
    })
  }

  down () {
    this.table('boat_images', (table) => {
      // reverse alternations
      table.dropColumn('DocumentID')
    })
  }
}

module.exports = BoatImagesSchema
