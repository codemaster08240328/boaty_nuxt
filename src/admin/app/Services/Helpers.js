'use strict'
const Request = require('request')
const moment = use('moment')

class SCHelpers {
  toTitleCase (str) {
    return str.replace(/\w\S*/g, function (txt) { return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase() })
  }

  DatesFromNow (date) {
    let thisWeek = null
    let nextWeek = 0

    if (date && moment(date) > new Date()) {
      const d = moment(date)
      thisWeek = date
      nextWeek = d.add(14, 'days').toISOString().substring(0, 10) // add two weeks by default
    } else {
      const d = new Date()
      const currentDay = d.getDate()

      for (let i = currentDay; i <= 730; i++) {
        const testDate = new Date(d.getFullYear(), d.getMonth(), i)
        if (testDate.getDay() === 6) {
          if (nextWeek === 1) {
            nextWeek = testDate.toISOString().substring(0, 10)
            break
          } else {
            thisWeek = testDate.toISOString().substring(0, 10)
          }
          nextWeek++
        }
      }
    }

    return [thisWeek, nextWeek]
  }

  async Request (options) {
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

  async postRequest (options) {
    return new Promise(function (resolve, reject) {
      Request.post(options, (err, res, body) => {
        if (err) reject(err)
        else {
          if (res.statusCode === 200) {
            const data = {
              data: body,
              res: res
            }
            resolve(data)
          } else {
            reject(err)
          }
        }
      })
    })
  }
}

module.exports = new SCHelpers()
