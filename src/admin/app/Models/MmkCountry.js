'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkCountry extends Model {
  static boot () {
    super.boot()

    this.addTrait('@provider:Lucid/Slugify', {
      fields: { slug: 'name' },
      strategy: 'dbIncrement',
      disableUpdates: false
    })
  }

  bases () {
    return this.hasMany('App/Models/MmkBase', 'id', 'country_id')
  }

  cities () {
    return this.hasMany('App/Models/MmkCity', 'id', 'country_id')
  }

  region () {
    return this.belongsTo('App/Models/MmkRegion', 'id', 'region_id')
  }

}

module.exports = MmkCountry
