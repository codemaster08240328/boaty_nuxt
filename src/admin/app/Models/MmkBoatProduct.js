'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkBoatProduct extends Model {
  boat () {
    return this.belongsTo('App/Models/MmkBoat', 'id', 'boat_id')
  }

  discounts () {
    return this.hasMany('App/Models/MmkProductDiscount', 'id', 'product_id')
  }

  extras () {
    return this.hasMany('App/Models/MmkProductExtra', 'id', 'product_id')
  }

  prices () {
    return this.hasMany('App/Models/MmkProductPrice', 'id', 'product_id')
  }
}

module.exports = MmkBoatProduct
