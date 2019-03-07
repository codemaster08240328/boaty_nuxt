'use strict'

const Schema = use('Schema')

class UsersSchema extends Schema {
  up () {
    this.table('users', (table) => {
      // alter table
      table.string('login_source').nullable()
      table.string('token').nullable()
    })
  }

  down () {
    this.table('users', (table) => {
      // reverse alternations
      table.dropColumn('login_source')
      table.dropColumn('token')
    })
  }
}

module.exports = UsersSchema
