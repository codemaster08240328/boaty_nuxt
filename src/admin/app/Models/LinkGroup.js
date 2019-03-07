'use strict'

const Model = use('Model')

class LinkGroup extends Model {
  pageLinks () {
    return this.hasMany('App/Models/PageLink')
  }
}

module.exports = LinkGroup
