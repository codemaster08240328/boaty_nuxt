'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkBoatType extends Model {
  boats () {
    return this.hasMany('App/Models/MmkBoat', 'id', 'model_id')
  }
}

module.exports = MmkBoatType
