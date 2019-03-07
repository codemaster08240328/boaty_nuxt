'use strict'
const Request = require('request')
const Config = use('Config')
const sharp = require('sharp')
const Drive = use('Drive')
const _ = use('lodash')
const querystring = require('querystring')

class APIImages {
  /*
    Upload individual images to S3
  */
  async imageToS3 (image, filename) {
    const config = Config.get('sailchecker')
    const s3Params = {
      'ContentType': 'image/jpeg',
      'ACL': 'public-read'
    }

    // see https://github.com/lovell/sharp for full details
    const imageOptions = {
      quality: 80
    }

    // add an overlay
    const thumbnail = sharp().resize(300, 300)
      .overlayWith('small-overlay-logo.png', { gravity: sharp.gravity.southeast })
      .jpeg(imageOptions)

    const medium = sharp().resize(600, 400)
      .overlayWith('small-overlay-logo.png', { gravity: sharp.gravity.southeast })
      .jpeg(imageOptions)

    const original = sharp()
      .overlayWith('overlay-logo.png', { gravity: sharp.gravity.southeast })
      .jpeg(imageOptions)

    /*
      this was the fastest way of uploading the images, all will upload asynchronously
      experimented with making other parts of the image processing async also but used far too much cpu, this gives a rest
    */
    try {
      await Promise.all([
        Drive.put(config.s3.boats + filename, image.pipe(original), s3Params),
        Drive.put(config.s3.boats + 'medium-' + filename, image.pipe(medium), s3Params),
        Drive.put(config.s3.boats + 'thumbnail-' + filename, image.pipe(thumbnail), s3Params)
      ])
    } catch (e) {
      console.log(e)
    }

    return true
  }

  /**
   * Uploads API images to AWS S3
   * Returns an array which is then persisted to DB
   * @param {Array of strings} images
   * @param {Integer} BoatID
   * @returns
   * @memberof APIImages
   */
  async imagesToS3 (images, BoatID) {
    let s3Images = [] // store image info in here to save later
    console.log(`uploading images for boat: ${BoatID}`)

    for (let i in images) {
      const imageUrl = images[i]
      const DocumentID = querystring.parse(imageUrl).DocumentID

      // create a file name using the BoatID and document_id from the image_url
      const filename = `${BoatID}-${DocumentID}.jpg`

      const image = await Request(imageUrl)

      // await this, if not, causes too much cpu usage because of sharp
      await this.imageToS3(image, filename)

      s3Images.push({
        BoatID: BoatID,
        URL: filename,
        DocumentID: DocumentID
      })
    }

    return s3Images
  }

  /**
   * Compares current images, with images from api and returns an array of image urls that do not exist
   * @param {Array of Objects} currentImages
   * @param {Array of strings} newImages
   * @param {integer} BoatID
   * @returns
   * @memberof APIImages
   */
  async findMatchingImages (currentImages, newImages, BoatID) {
    newImages = _.filter(newImages, (ni) => {
      const DocumentID = querystring.parse(ni).DocumentID
      if (!DocumentID) {
        return false
      }

      const filename = `${BoatID}-${DocumentID}.jpg`
      let match = _.findIndex(currentImages, (v) => { return filename === v.url }) !== -1

      if (match === -1) {
        return false
      } else {
        return true
      }
    })

    return newImages
  }
}

module.exports = new APIImages()
