'use strict'

const Schema = use('Schema')

class BoatExtrasSchema extends Schema {
  up () {
    this.table('boat_extras', (table) => {
      // alter table
      table.integer('BoatEquipmentID').unsigned()
    })
  }

  down () {
    this.table('boat_extras', (table) => {
      // reverse alternations
      table.dropColumn('BoatEquipmentID')
    })
  }
}

module.exports = BoatExtrasSchema
