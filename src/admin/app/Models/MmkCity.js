'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

class MmkCity extends Model {
  static boot () {
    super.boot()

    this.addTrait('@provider:Lucid/Slugify', {
      fields: { slug: 'name' },
      strategy: 'dbIncrement',
      disableUpdates: false
    })
  }

  bases () {
    return this.hasMany('App/Models/MmkBase', 'id', 'city_id')
  }

  country () {
    return this.belongsTo('App/Models/MmkCountry', 'id', 'country_id')
  }
}

module.exports = MmkCity
