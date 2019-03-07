'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkBase extends Model {
  static boot () {
    super.boot()

    this.addTrait('@provider:Lucid/Slugify', {
      fields: { slug: 'name' },
      strategy: 'dbIncrement',
      disableUpdates: false
    })
  }

  boatDiscounts () {
    return this.hasMany('App/Models/MmkBoatDiscount', 'id', 'base_id')
  }

  boatExtras () {
    return this.hasMany('App/Models/MmkBoatExtra', 'id', 'base_id')
  }

  boatLocations () {
    return this.hasMany('App/Models/MmkBoatLocation', 'id', 'base_id')
  }

  boats () {
    return this.hasMany('App/Models/MmkBoat', 'id', 'base_id')
  }

  cityModel () { // city is already name of the column
    return this.belongsTo('App/Models/MmkCity', 'id', 'city_id')
  }

  country () {
    return this.belongsTo('App/Models/MmkCountry', 'id', 'country_id')
  }

  productDiscounts () {
    return this.hasMany('App/Models/MmkProductDiscount', 'id', 'base_id')
  }

  productExtras () {
    return this.hasMany('App/Models/MmkProductExtra', 'id', 'base_id')
  }

  regions () {
    return this.belongsToMany('App/Models/MmkRegion')
      .pivotModel('App/Models/MmkBaseRegion')
  }

  saturdayBoats () {
    return this.hasMany('App/Models/MmkBoat', 'id', 'saturday_base_id')
  }
}

module.exports = MmkBase
