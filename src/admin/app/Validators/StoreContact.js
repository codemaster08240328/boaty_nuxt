'use strict'

class StoreContact {
  get rules () {
    return {
      adults: 'number',
      base_id: 'number',
      boat_id: 'number',
      boat_price_euro: 'number',
      children: 'number',
      currency: 'alpha',
      date: 'date',
      email: 'email',
      exchange_rate: 'number',
      message: 'string',
      phone_bumber: 'string',
      weeks: 'number'
    }
  }

  get sanitizationRules () {
    return {
      adults: 'to_int',
      base_id: 'to_int',
      boat_id: 'to_int',
      boat_price_euro: 'to_int',
      children: 'to_int',
      weeks: 'to_int'
    }
  }

  async fails (errorMessages) {
    return this.ctx.response.status(422).send(errorMessages)
  }
}

module.exports = StoreContact
