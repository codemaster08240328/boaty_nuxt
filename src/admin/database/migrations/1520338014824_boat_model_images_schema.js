'use strict'

const Schema = use('Schema')

class BoatModelImagesSchema extends Schema {
  up () {
    this.table('boat_model_images', (table) => {
      // alter table
      table.integer('DocumentID').unsigned()
    })
  }

  down () {
    this.table('boat_model_images', (table) => {
      // reverse alternations
      table.dropColumn('DocumentID')
    })
  }
}

module.exports = BoatModelImagesSchema
