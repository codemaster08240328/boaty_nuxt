'use strict'

/** @type {import('@adonisjs/lucid/src/Schema')} */
const Schema = use('Schema')

class MmkCompanySchema extends Schema {
  up () {
    this.create('mmk_companies', (table) => {
      table.increments()
      table.integer('mmk_id').unsigned().unique()
      table.string('name')
      table.string('address')
      table.string('city')
      table.string('zip')
      table.string('country_name')
      table.string('phone')
      table.string('phone2')
      table.string('fax')
      table.string('fax2')
      table.string('mobile')
      table.string('mobile2')
      table.string('vat_code')
      table.string('email')
      table.string('web')
      table.integer('availability')
      table.timestamps()
    })
  }

  down () {
    this.drop('mmk_companies')
  }
}

module.exports = MmkCompanySchema
