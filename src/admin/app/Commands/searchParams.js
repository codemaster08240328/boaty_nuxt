'use strict'

const { Command } = require('@adonisjs/ace')
const Database = use('Database')
const querystring = require('querystring')
const Country = use('App/Models/Country')
const Area = use('App/Models/Area')
const Base = use('App/Models/Base')
const BoatBrand = use('App/Models/BoatBrand')
const BoatModel = use('App/Models/BoatModel')
const BoatType = use('App/Models/BoatType')
const Cabin = use('App/Models/Cabin')
const FleetOperator = use('App/Models/FleetOperator')
const Env = use('Env')
const SCHelpers = use('App/Services/Helpers')

class searchParams extends Command {
  static get signature () {
    return `
      bbapi:searchParams
    `
  }

  static get description () {
    return 'Update Database with BB api info'
  }

  async handle (args, options) {
    await this.searchParams()

    console.log('completed')

    Database.close()
  }

  async save () {

  }

  async searchParams () {
    /*
      User credentials, refer to BB web service for search engine for extra params
    */
    const form = {
      username: Env.get('BB_USERNAME'),
      password: Env.get('BB_PASSWORD'),
      pid: Env.get('BB_PID')
    }

    const options = {
      url: Env.get('BB_SEARCH_REQUEST_URL') + querystring.stringify(form),
      headers: {
        'content-type': 'application/x-www-form-urlencoded',
        'Accept-Encoding': 'gzip,deflate'
      },
      gzip: true
    }

    let data = null

    try {
      data = await SCHelpers.Request(options)
    } catch (e) {
      console.log(e)
    }

    console.log(Object.keys(data))

    /*
      Splits countries up, and finds or creates them TODO, update if they are not the same!
      Same applies for code below
    */
    let countries = []
    for (let key of Object.keys(data.Countries)) {
      const country = await Country.findOrCreate(
        { ID: data.Countries[key].ID },
        data.Countries[key]
      )

      countries.push(country)
    }

    let areas = []
    for (let key of Object.keys(data.Areas)) {
      const area = await Area.findOrCreate(
        { ID: data.Areas[key].ID },
        data.Areas[key]
      )

      areas.push(area)
    }

    let bases = []
    for (let key of Object.keys(data.Bases)) {
      const base = await Base.findOrCreate(
        { ID: data.Bases[key].ID },
        data.Bases[key]
      )

      bases.push(base)
    }

    let boattypes = []
    for (let key of Object.keys(data.BoatTypes)) {
      const boattype = await BoatType.findOrCreate(
        { ID: data.BoatTypes[key].ID },
        data.BoatTypes[key]
      )

      boattypes.push(boattype)
    }

    let boatbrands = []
    for (let key of Object.keys(data.BoatBrands)) {
      const boatbrand = await BoatBrand.findOrCreate(
        { ID: data.BoatBrands[key].ID },
        data.BoatBrands[key]
      )

      boatbrands.push(boatbrand)
    }

    let boatmodels = []
    for (let key of Object.keys(data.BoatModels)) {
      const boatmodel = await BoatModel.findOrCreate(
        { ID: data.BoatModels[key].ID },
        data.BoatModels[key]
      )

      boatmodels.push(boatmodel)
    }

    let cabins = []
    for (let key of Object.keys(data.Cabins)) {
      const cabin = await Cabin.findOrCreate(
        { ID: data.Cabins[key].ID },
        data.Cabins[key]
      )

      cabins.push(cabin)
    }

    let fleetoperators = []
    for (let key of Object.keys(data.FleetOperators)) {
      const fleetoperator = await FleetOperator.findOrCreate(
        { ID: data.FleetOperators[key].ID },
        data.FleetOperators[key]
      )

      fleetoperators.push(fleetoperator)
    }

    return true
  }
}

module.exports = searchParams
