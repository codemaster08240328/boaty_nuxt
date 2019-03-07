'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkProductExtra extends Model {
  base () {
    return this.belongsTo('App/Models/MmkBase', 'id', 'base_id')
  }

  product () {
    return this.belongsTo('App/Models/MmkBoatProduct', 'id', 'product_id')
  }
}

module.exports = MmkProductExtra
