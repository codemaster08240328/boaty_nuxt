'use strict'

const Schema = use('Schema')

class LinkGroupsSchema extends Schema {
  up () {
    this.create('link_groups', (table) => {
      table.increments()
      table.timestamps()
      table.string('name').notNullable()
      table.integer('user_id').notNullable().unsigned()
      table.unique(['name'], 'unique_name')
      table.index('id')
    })
  }

  down () {
    this.drop('link_groups')
  }
}

module.exports = LinkGroupsSchema
