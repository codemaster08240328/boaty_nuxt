'use strict'

const { hooks } = require('@adonisjs/ignitor')
const _ = use('lodash')

hooks.after.httpServer(() => {
  use('App/Services/Nuxt')
})

hooks.after.providersBooted(() => {
  const Database = use('Database')

  Database.insertUpdate = (table, data, pks) => {
    const firstRow = data[0] ? data[0] : data

    if (typeof pks === 'undefined') {
      pks = []
    }
    const fields = _.difference(_.keys(firstRow), pks)

    const fieldsStr = _.map(fields, (o) => {
      return ('`%s` = VALUES(`%s`)').replace(/%s/gi, o)
    })

    const query = Database.table(table).insert(data).toQuery() + ' ON DUPLICATE KEY UPDATE ' + fieldsStr.join(', ').toString()

    return Database.raw(query)
  }
  // Database.on('query', console.log)
})
