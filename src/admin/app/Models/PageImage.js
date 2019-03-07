'use strict'

const Model = use('Model')

class PageImage extends Model {
  page () {
    return this.belongsTo('App/Models/Page')
  }

  pageChequer () {
    return this.belongsTo('App/Models/PageChequer')
  }
}

module.exports = PageImage
