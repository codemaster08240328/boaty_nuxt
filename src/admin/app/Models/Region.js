'use strict'

const Model = use('Model')

class Region extends Model {
  pages () {
    return this.hasMany('App/Models/Page')
  }
}

module.exports = Region
