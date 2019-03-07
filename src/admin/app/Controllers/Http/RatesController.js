'use strict'
const Request = require('request')
const Drive = use('Drive')

class RatesController {
  // implement this in a provider
  async callback (options) {
    return new Promise(function (resolve, reject) {
      Request(options, (err, res, body) => {
        if (err) reject(err)
        else {
          if (res.statusCode === 200) {
            const data = JSON.parse(body)
            resolve(data)
          } else {
            reject(err)
          }
        }
      })
    })
  }

  async getRates ({ request, response }) {
    const options = {
      url: 'https://openexchangerates.org/api/latest.json?app_id=0234aabe5d9549fdaef0938e517e6de7&symbols=eur,gbp',
      headers: {
        'content-type': 'application/x-www-form-urlencoded',
        'Accept-Encoding': 'gzip,deflate'
      },
      gzip: true
    }

    let data = await this.callback(options)

    delete data.disclaimer
    delete data.license
    delete data.timestamp

    data = this.swapRates(data)

    await Drive.put('uploads/json/rates.json', Buffer.from(JSON.stringify(data)), {
      'ContentType': 'application/javascript',
      'ACL': 'public-read'
    })

    return data
  }
  /**
   * @description makes euro base currency
   * 
   * @param {any} data 
   * @returns 
   * @memberof RatesController
   */
  swapRates (data) {
    data.base = 'EUR'
    data.rates['USD'] = 1 / data.rates['EUR']
    data.rates['GBP'] = data.rates['GBP'] / data.rates['EUR']
    data.rates['EUR'] = 1

    return data
  }
}

module.exports = RatesController
