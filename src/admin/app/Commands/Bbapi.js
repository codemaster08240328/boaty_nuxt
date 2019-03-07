'use strict'

const { Command } = require('@adonisjs/ace')
const Database = use('Database')
const querystring = require('querystring')
const _ = use('lodash')
const Boat = use('App/Models/Boat')
const Env = use('Env')
const SCHelpers = use('App/Services/Helpers')
const APIImages = use('App/Services/APIImages')
const Mail = use('Mail')

class Bbapi extends Command {
  static get signature () {
    return `
      bbapi:getSearchListings
      { --images=@value : set to true to sync images }
    `
  }

  static get description () {
    return 'Update Database with BB api info'
  }

  async handle (args, options) {
    console.log('Starting API sync')

    const data = await this.getCompleteBoatListing(options)

    await Mail.send('emails.getSearchListings', data, (message) => {
      message
        .to('matt@saowapan.com')
        .from('info@sailchecker.com')
        .subject('BBAPI getSearchListings Completed')
    })

    Database.close()
  }

  async save (data, options) {
    let BoatIDS = []
    let l = data.length

    while (l--) {
      /*
        this entire while loop code could be put into an object/array and be handled automatically, but right now
        the extra lines of code don't hinder the maintainability but offer more control,
        many of the API docs appear to be "guidelines" and I expect this function to change as we incorporate other parts of the BB api
        as well as starting to include sedna api etc.
      */
      let Boatd = data[l].Boat
      let boatImages = Boatd.BoatImages
      let boatModelImages = Boatd.BoatModelImages
      let PrimaryBase = data[l].PrimaryBase
      // let AlternativeBases = data[l].AlternativeBases
      let Periods = data[l].Periods
      // let PeriodsDiscount = Periods.Discounts

      // clean up Boatd
      delete Boatd.BoatImages
      delete Boatd.BoatModelImages
      delete Boatd.Equipment

      BoatIDS.push(Boatd.ID)
      Boatd.status = 1

      try {
        let boat = await Boat.findOrCreate(
          { ID: Boatd.ID },
          Boatd
        )

        boat.merge(Boatd)
        await boat.save()

        if (options && options.images === 'true') {
          if (boatImages.length !== 0) {
            boatImages = _.map(boatImages, (i) => { return i.URL })
            const currentBoatImages = await Database.select('url').from('boat_images').where('BoatID', Boatd.ID)
            const sortedImages = await APIImages.findMatchingImages(currentBoatImages, boatImages, Boatd.ID)

            if (sortedImages.length !== 0) {
              const s3Images = await APIImages.imagesToS3(sortedImages, Boatd.ID)
              await Database.insertUpdate('boat_images', s3Images)
            }
          }

          if (boatModelImages.length !== 0) {
            boatModelImages = _.map(boatModelImages, (i) => { return i.URL })
            const currentBoatModelImages = await Database.select('url').from('boat_model_images').where('BoatID', Boatd.ID)

            const sortedImages = await APIImages.findMatchingImages(currentBoatModelImages, boatModelImages, Boatd.ID)

            if (sortedImages.length !== 0) {
              const s3Images = await APIImages.imagesToS3(sortedImages, Boatd.ID)
              await Database.insertUpdate('boat_model_images', s3Images)
            }
          }
        }

        if (PrimaryBase.length !== 0) {
          if (PrimaryBase.ID === 0) {
            boat.status = 0
            await boat.save()
          }

          PrimaryBase.isPrimary = 1
          PrimaryBase.BoatID = Boatd.ID

          let currentBases = await Database.select('BoatID', 'ID', 'Name', 'isPrimary').from('boat_bases').where({ BoatID: Boatd.ID })

          await Database.insertUpdate('boat_bases', PrimaryBase)

          let diff = _.differenceWith(currentBases, [PrimaryBase], _.isEqual)
          // delete boat bases if they change
          if (diff.length > 0) {
            console.log('difference', currentBases, PrimaryBase)
            for (let d of diff) {
              await Database.table('boat_bases').where(d).del()
            }
          }
        } else {
          console.log('no base')
        }

        if (Periods.length !== 0) {
          // clean up periods
          Periods = _.map(Periods, (p) => {
            p.BookedOrOptionBaseFrom = p.BookedOrOptionBaseFrom.ID
            p.BookedOrOptionBaseTo = p.BookedOrOptionBaseTo.ID
            p.BoatID = Boatd.ID
            delete p.OptionExpiryDate
            p.CurrencyCode = 'EUR'
            delete p.Discounts
            return p
          })

          await Database.insertUpdate('boat_periods', Periods)
        }
      } catch (e) {
        console.log(e)
      }
    }
    return BoatIDS
  }

  async getCompleteBoatListing (options) {
    const d = new Date()

    let emailValues = {
      deactivated_boats: []
    }

    /*
      Use only the minimum params, to get full listings
    */
    const form = {
      username: Env.get('BB_USERNAME'),
      password: Env.get('BB_PASSWORD'),
      pid: Env.get('BB_PID'),
      count: 100,
      orderBy: 1,
      departureYear: d.getFullYear(),
      departureMonth: d.getMonth() + 1,
      duration: 7,
      numberOfNeighbourPeriods: 70
    }

    const bb = {
      url: 'https://app.boatbooker.net/SearchEngineAPI/Search/SearchBoats?' + querystring.stringify(form),
      headers: {
        'content-type': 'application/x-www-form-urlencoded',
        'Accept-Encoding': 'gzip,deflate'
      },
      gzip: true,
      timeout: 60 * 30 * 1000
    }

    const allBoatIDS = await Boat.ids()

    let data = null

    try {
      data = await SCHelpers.Request(bb)
    } catch (e) {
      console.log(e)
      return true
    }

    // save the first lot of boats received from API, save the rest in a while loop later
    let BoatIDS = await this.save(data.Results, options)

    // work out how many times BB api will need to be called to return all search results
    let loopsRequired = Math.ceil(data.TotalResults / form.count)
    const loops = loopsRequired

    while (loopsRequired--) {
      let ids = null
      let data = null
      console.log('Search Results API calls until sync complete: ' + loopsRequired)

      form.offset = (loops - loopsRequired) * form.count
      form.skipGetTotalResult = 1

      bb.url = 'https://app.boatbooker.net/SearchEngineAPI/Search/SearchBoats?' + querystring.stringify(form)

      try {
        data = await SCHelpers.Request(bb)
        if (data.Results.length > 0) {
          ids = await this.save(data.Results, options)
          console.log('saved boats, concatting BoatIDS')
          BoatIDS.push(...ids)
        }
      } catch (e) {
        console.log(`Search Results API sync failed on loop: ${loopsRequired}`)
        console.log(e)
      }
    }

    emailValues.deactivated_boats = _.difference(allBoatIDS, BoatIDS)

    try {
      console.log('switching boats to inactive')
      await Boat
        .query()
        .whereIn('id', emailValues.deactivated_boats)
        .update({ status: 0 })
    } catch (e) {
      console.log(e)
    }

    return emailValues
  }
}

module.exports = Bbapi
