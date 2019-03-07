'use strict'

const moment = require('moment')

// Purpose of this service is to find price and location of the boat on next saturday
class MmkSaturdayService {
  constructor (boat)  {
    this.boat = boat

    // Next saturday
    if (moment.utc().weekday() === 6) {
      this.saturday = moment.utc().day(13).startOf('day').toDate() // Saturday next week
    } else {
      this.saturday = moment.utc().day(6).startOf('day').toDate() // Saturday this week
    }
  }

  async fillDetails () {
    await this.setupPrice()
    await this.setupBase()

    await this.boat.save()
  }

  async setupPrice () {
    // First try to find price for next saturday
    let price = await this.boat.prices()
      .where('date_from', '<=', this.saturday)
      .where('date_to', '>=', this.saturday)
      .first()

    if (price !== null) {
      this.boat.saturday_price = price.price
      return
    }

    // If none found, get the nearest possible price
    price = await this.boat.prices()
      .orderBy('date_to', 'asc')
      .where('date_to', '>', new Date())
      .first()


    // Again, if nothing found, set price to 0
    if (price === null) {
      this.boat.saturday_price = 0.0
    } else {
      this.boat.saturday_price = price.price
    }
  }

  async setupBase () {
    // If we are not having custom base set for next saturday, use default base ID
    let location = await this.boat.locations()
      .where('date_from', '<=', this.saturday)
      .where('date_to', '>=', this.saturday)
      .first()

    if (location === null) {
      this.boat.saturday_base_id = this.boat.base_id
    } else {
      this.boat.saturday_base_id = location.base_id
    }
  }
}

module.exports = MmkSaturdayService
