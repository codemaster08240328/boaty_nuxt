'use strict'

const Schema = use('Schema')

class CountriesSchema extends Schema {
  up () {
    this.table('countries', (table) => {
      // alter table
      table.dropUnique('ID', 'ID_UNIQUE')
    })
  }

  down () {
    this.table('countries', (table) => {
      // reverse alternations
      table.unique('ID', 'ID_UNIQUE')
    })
  }
}

module.exports = CountriesSchema
