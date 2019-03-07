'use strict'

/** @type {typeof import('@adonisjs/lucid/src/Lucid/Model')} */
const Model = use('Model')

/* Information about region from MMK */
class MmkRegion extends Model {
  static boot () {
    super.boot()

    this.addTrait('@provider:Lucid/Slugify', {
      fields: { slug: 'name' },
      strategy: 'dbIncrement',
      disableUpdates: false
    })
  }

  countries () {
    return this.hasMany('App/Models/MmkCountry', 'id', 'region_id')
  }

  bases () {
    return this.belongsToMany('App/Models/MmkBase')
      .pivotModel('App/Models/MmkBaseRegion')
  }
}

module.exports = MmkRegion
