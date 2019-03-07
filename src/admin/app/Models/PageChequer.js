'use strict'

const Model = use('Model')

class PageChequer extends Model {
  page () {
    return this.belongsTo('App/Models/Page')
  }
  pageImage () {
    return this.hasOne('App/Models/PageImage')
  }
}

module.exports = PageChequer
