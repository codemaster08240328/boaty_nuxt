
'use strict'

const { Command } = require('@adonisjs/ace')
const Database = use('Database')
const querystring = require('querystring')
const SCHelpers = use('App/Services/Helpers')
const Env = use('Env')
const APIImages = use('App/Services/APIImages')

class getBoatImages extends Command {
  static get signature () {
    return `
      bbapi:getBoatImages 
    `
  }

  static get description () {
    return 'Update boat equipment, extras and images'
  }

  async handle (args, options) {
    await this.getBoatImages()

    console.log('completed')

    Database.close()
  }

  async getBoatImages () {
    /*
      Use only the minimum params, to get full listings
    */
    const form = {
      username: Env.get('BB_USERNAME'),
      password: Env.get('BB_PASSWORD'),
      lang: 'en'
    }
    console.log(form)
    const options = {
      headers: {
        'content-type': 'application/x-www-form-urlencoded',
        'Accept-Encoding': 'gzip,deflate'
      },
      gzip: true,
      timeout: 60 * 30 * 1000
    }

    // get a list of a
    const BoatIDS = await Database.select('ID').from('boats').where('status', 1)
    let index = 0

    while (BoatIDS.length > index) {
      let togo = BoatIDS.length - index
      form.boatid = BoatIDS[index].ID
      console.log(`Working on Boat ID: ${form.boatid}, there are ${togo} boats left`)
      options.url = 'https://api.boatbooker.net/ws/boatinfo/getboatinfo?' + querystring.stringify(form)
      try {
        const data = await SCHelpers.Request(options)

        let boatImages = data.BoatImages
        let boatModelImages = data.BoatModelImages

        if (boatImages.length !== 0) {
          const currentBoatImages = await Database.select('url').from('boat_images').where('BoatID', form.boatid)
          const sortedImages = await APIImages.findMatchingImages(currentBoatImages, boatImages, form.boatid)

          if (sortedImages.length !== 0) {
            const s3Images = await APIImages.imagesToS3(sortedImages, form.boatid)
            await Database.insertUpdate('boat_images', s3Images)
          }
        }

        if (boatModelImages.length !== 0) {
          const currentBoatModelImages = await Database.select('url').from('boat_model_images').where('BoatID', form.boatid)
          const sortedImages = await APIImages.findMatchingImages(currentBoatModelImages, boatModelImages, form.boatid)

          if (sortedImages.length !== 0) {
            const s3Images = await APIImages.imagesToS3(sortedImages, form.boatid)
            await Database.insertUpdate('boat_model_images', s3Images)
          }
        }
      } catch (e) {
        console.log(e)
      }
      index++
    }

    return true
  }
}

module.exports = getBoatImages
