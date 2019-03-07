'use strict'

const Model = use('Model')

class PageLink extends Model {
  page () {
    return this.belongsTo('App/Models/Page')
  }
  linkGroup () {
    return this.belongsTo('App/Models/LinkGroup')
  }
}

module.exports = PageLink
