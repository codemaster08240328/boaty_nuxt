'use strict'

const Model = use('Model')

class PageBoatType extends Model {
  static get createdAtColumn () {
    return null
  }

  static get updatedAtColumn () {
    return null
  }

  page () {
    return this.belongsTo('App/Models/Page')
  }
}

module.exports = PageBoatType
