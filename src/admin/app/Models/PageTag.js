'use strict'

const Model = use('Model')

class PageTag extends Model {
  page () {
    return this.belongsTo('App/Models/Page')
  }
}

module.exports = PageTag
