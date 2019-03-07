'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkCompany extends Model {
  boats () {
    return this.hasMany('App/Models/MmkBoat', 'id', 'company_id')
  }

  /*
   * Availability types:
   * 0 - realtime
   * 1 - periodic
   * 2 - rare
   */
}

module.exports = MmkCompany
