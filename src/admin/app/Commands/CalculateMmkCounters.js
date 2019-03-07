'use strict'

const { Command } = require('@adonisjs/ace')

const MmkCounterService = use('App/Services/MmkCounterService')

class CalculateMmkCounters extends Command {
  static get signature () {
    return 'mmk:update-counters'
  }

  static get description () {
    return 'Update information about number of boats in every base/region'
  }

  async handle (args, options) {
    let updateService = new MmkCounterService()
    await updateService.updateCounters()
  }
}

module.exports = CalculateMmkCounters
