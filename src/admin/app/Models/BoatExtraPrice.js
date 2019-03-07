'use strict'

const Model = use('Model')

class BoatExtraPrice extends Model {
  BoatExtraPriceDate () {
    return this.hasMany('App/Models/BoatExtraPriceDate', 'ExtraID')
  }
}

module.exports = BoatExtraPrice
