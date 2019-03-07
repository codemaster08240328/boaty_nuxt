'use strict'

const Schema = use('Schema')

class UsersSchema extends Schema {
  up () {
    this.table('users', (table) => {
      // alter table
      table.boolean('is_admin').notNullable()
    })
  }

  down () {
    this.table('users', (table) => {
      // reverse alternations
      table.dropColumn('is_admin')
    })
  }
}

module.exports = UsersSchema
