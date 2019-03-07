'use strict'

const Database = use('Database')

const MmkBase = use('App/Models/MmkBase')
const MmkBaseCount = use('App/Models/MmkBaseCount')
const MmkBoatLocation = use('App/Models/MmkBoatLocation')
const MmkRegion = use('App/Models/MmkRegion')
const MmkRegionCount = use('App/Models/MmkRegionCount')

class MmkCounterService {
  constructor () {
    // List of processed boats with IDs
    this.today = new Date()
    this.bases = []
    this.regionData = {}
    this.customIds = [] // List of boats with custom location set
    this.baseBoats = {} // base ID -> [list of boat IDs within given boat]
  }

  async updateCounters () {
    await this.loadBases()

    console.log('Parsing custom locations...')
    await this.processCustomLocations()

    console.log('Processing default locations...')
    let total = this.bases.length
    for (let i = 0; i < total; i++) {
      await this.processSingleBase(this.bases[i])
    }

    console.log('Generating base statistics...')
    await this.generateBaseCounters()

    console.log('Generating region statistics...')
    await this.generateRegionCounters()

    Database.close()
  }

  async loadBases () {
    let dbBases = await MmkBase.all()
    this.bases = dbBases.rows

    let total = this.bases.length
    for(let i = 0; i < total; i++) {
      this.baseBoats[this.bases[i].id] = []
    }
  }

  async processCustomLocations () {
    let dbLocations = await MmkBoatLocation
      .query()
      .where('default_base', false)
      .where('date_from', '<=', this.today)
      .where('date_to', '>=', this.today)
      .fetch()

    dbLocations = dbLocations.rows
    let total = dbLocations.length

    for (let i = 0; i < total; i++) {
      let location = dbLocations[i]
      this.customIds.push(location.boat_id)
      this.baseBoats[location.base_id].push(location.boat_id)
    }
  }

  async processSingleBase (base) {
    let locations = await base.boatLocations().defaultBase().fetch()
    locations = locations.rows

    let total = locations.length
    for (let i = 0; i < total; i++) {
      let location = locations[i]
      if (!this.customIds.includes(location.boat_id)) {
        this.baseBoats[base.id].push(location.boat_id)
      }
    }
  }

  async generateBaseCounters () {
    // First remove existing data and then generate new set
    await MmkBaseCount.query().delete()

    let total = this.bases.length
    for(let i = 0; i < total; i++) {
      let id = this.bases[i].id
      let count = new MmkBaseCount()
      count.base_id = id
      count.calculation_date = this.today
      count.boats_count = this.baseBoats[id].length
      count.boat_ids = this.baseBoats[id].join(',')
      await count.save()
    }
  }

  async generateRegionCounters () {
    // First remove existing data and then generate new set
    await MmkRegionCount.query().delete()

    let regions = await MmkRegion.all()
    regions = regions.rows

    let total = regions.length
    for (let i = 0; i < total; i++) {
      let region = regions[i]
      let count = new MmkRegionCount()
      count.region_id = region.id
      count.lat = 0.0
      count.lng = 0.0
      count.boats_count = 0

      let regionBases = await region.bases().fetch()
      regionBases = regionBases.rows

      let x = 0.0

      let baseTotal = regionBases.length
      for (let j = 0; j < baseTotal; j++) {
        let base = regionBases[j]
        count.boats_count += this.baseBoats[base.id].length
        if (base.longitude !== null && base.latitude !== null) {
          count.lat += base.latitude
          count.lng += base.longitude
          x += 1.0
        }
      }

      if (x > 0.0) {
        count.lat /= x
        count.lng /= x
      }

      await count.save()
    }
  }

  async generateCounters() {
    // First remove existing data and then generate new set
    await MmkBaseCount.query().delete()
    await MmkRegionCount.query().delete()

    let total = this.bases.length
    for (let i = 0; i < total; i++) {
      let bases = this.bases[i]

      if (typeof this.regionData[base.region_id] === 'undefined') {
        this.regionData[base_region_id] = {
          lats: [],
          lngs: [],
          boatsCount: 0,
          prices: []
        }
      }
    }
  }
}

module.exports = MmkCounterService
