'use strict'

const soap = require('soap')
const wsdlUrl = 'https://www.booking-manager.com/cbm_web_service2/services/CBM?wsdl'

class MMKApiClient {
  async getCompanies () {
    let args = this.defaultArgs()
    return new Promise(function (resolve, reject) {
      soap.createClient(wsdlUrl, function (err, client) {
        if (err) {
          console.error('ERROR: Could not create SOAP client')
          console.error(err)
          reject(err)
        }

        client.getCompanies(args, function (err, response) {
          if (err) {
            console.error('SOAP error when trying to fetch company list')
            console.error(err)
            return resolve(null)
          }

          return resolve(response.out)
        }, {forever: false, timeout: 120000})
      })
    })
  }

  async getResources (companyId) {
    let args = this.defaultArgs()
    args['companyId'] = companyId

    return new Promise(function (resolve, reject) {
      soap.createClient(wsdlUrl, function (err, client) {
        if (err) {
          console.error('ERROR: Could not create SOAP client')
          console.error(err)
          reject(err)
        }

        client.getResources(args, function (err, response) {
          if (err) {
            console.error('SOAP error when trying to fetch resources list')
            console.error(err)
            return resolve(null)
          }

          return resolve(response.out)
        }, {forever: false, timeout: 120000})
      })
    })
  }

  async getBases () {
    let args = this.defaultArgs()
    return new Promise(function (resolve, reject) {
      soap.createClient(wsdlUrl, function (err, client) {
        if (err) {
          console.error('ERROR: Could not create SOAP client')
          console.error(err)
          reject(err)
        }

        client.getBases(args, function (err, response) {
          if (err) {
            console.error('SOAP error when trying to fetch bases list')
            console.error(err)
            return resolve(null)
          }

          return resolve(response.out)
        }, {forever: false, timeout: 120000})
      })
    })
  }

  async getRegions () {
    let args = this.defaultArgs()
    return new Promise(function (resolve, reject) {
      soap.createClient(wsdlUrl, function (err, client) {
        if (err) {
          console.error('ERROR: Could not create SOAP client')
          console.error(err)
          reject(err)
        }

        client.getRegions(args, function (err, response) {
          if (err) {
            console.error('SOAP error when trying to fetch regions list')
            console.error(err)
            return resolve(null)
          }

          return resolve(response.out)
        }, {forever: false, timeout: 120000})
      })
    })
  }

  async getCountries () {
    let args = this.defaultArgs()
    return new Promise(function (resolve, reject) {
      soap.createClient(wsdlUrl, function (err, client) {
        if (err) {
          console.error('ERROR: Could not create SOAP client')
          console.error(err)
          reject(err)
        }

        client.getCountries(args, function (err, response) {
          if (err) {
            console.error('SOAP error when trying to fetch countries')
            console.error(err)
            return resolve(null)
          }

          return resolve(response.out)
        }, {forever: false, timeout: 120000})
      })
    })
  }

  async getEquipmentCategories () {
    let args = this.defaultArgs()
    return new Promise(function (resolve, reject) {
      soap.createClient(wsdlUrl, function (err, client) {
        if (err) {
          console.error('ERROR: Could not create SOAP client')
          console.error(err)
          reject(err)
        }

        client.getEquipmentCategories(args, function (err, response) {
          if (err) {
            console.error('SOAP error when trying to fetch equipment categories')
            console.error(err)
            return resolve(null)
          }

          return resolve(response.out)
        }, {forever: false, timeout: 120000})
      })
    })
  }

  defaultArgs () {
    return {
      userId: process.env.MMK_API_CLIENT_ID,
      username: process.env.MMK_API_EMAIL,
      password: process.env.MMK_API_PASSWORD
    }
  }
}

module.exports = new MMKApiClient()
