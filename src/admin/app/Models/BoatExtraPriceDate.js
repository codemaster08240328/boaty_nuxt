'use strict'

const Model = use('Model')

class BoatExtraPriceDate extends Model {
  static get primaryKey () {
    return 'PriceID'
  }

  BoatExtraPrice () {
    return this.belongsTo('App/Models/BoatExtraPrice')
  }
}

module.exports = BoatExtraPriceDate
