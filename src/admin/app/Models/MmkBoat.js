'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkBoat extends Model {
  base () {
    return this.belongsTo('App/Models/MmkBase', 'id', 'base_id')
  }

  boatServiceType () { // service_type is name of attribute
    return this.belongsTo('App/Models/MmkBoatServiceType', 'id', 'service_type_id')
  }

  boatType () {
    return this.belongsTo('App/Models/MmkBoatType', 'id', 'model_id')
  }

  categories () {
    return this.hasMany('App/Models/MmkBoatCategory', 'id', 'boat_id')
  }

  company () {
    return this.belongsTo('App/Models/MmkCompany', 'id', 'company_id')
  }

  discounts () {
    return this.hasMany('App/Models/MmkBoatDiscount', 'id', 'boat_id')
  }

  equipments () {
    return this.hasMany('App/Models/MmkBoatEquipment', 'id', 'boat_id')
  }

  extras () {
    return this.hasMany('App/Models/MmkBoatExtra', 'id', 'boat_id')
  }

  genericType () {
    return this.belongsTo('App/Models/MmkBoatGenericType', 'id', 'generic_type_id')
  }

  images () {
    return this.hasMany('App/Models/MmkBoatImage', 'id', 'boat_id')
  }

  boatKind () {
    return this.hasMany('App/Models/MmkBoatKind', 'id', 'kind_id')
  }

  locations () {
    return this.hasMany('App/Models/MmkBoatLocation', 'id', 'boat_id')
  }

  prices () {
    return this.hasMany('App/Models/MmkBoatPrice', 'id', 'boat_id')
  }

  products () {
    return this.hasMany('App/Models/MmkBoatProduct', 'id', 'boat_id')
  }

  saturdayBase () {
    return this.belongsTo('App/Models/MmkBase', 'id', 'saturday_base_id')
  }
}

module.exports = MmkBoat
