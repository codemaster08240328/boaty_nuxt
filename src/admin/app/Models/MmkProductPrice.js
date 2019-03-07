'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkProductPrice extends Model {
  product () {
    return this.belongsTo('App/Models/MmkBoatProduct', 'id', 'product_id')
  }
}

module.exports = MmkProductPrice
