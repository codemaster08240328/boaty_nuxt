'use strict'
const Database = use('Database')
const Page = use('App/Models/Page')
// const PageImage = use('App/Models/PageImage')
const Drive = use('Drive')
const Config = use('Config')
const sharp = require('sharp')
const Destinations = use('App/Services/Destinations')
const _ = use('lodash')

class ContentController {
  async index ({ params }) {
    const result = await Page
      .query()
      .where('type', params.type)
      .with('pageCategory')
      .with('pageImage')
      .with('pageChequer', (builder) => {
        builder.orderBy('order', 'asc')
      })
      .with('pageBoatBrand')
      .with('pageBoatType')
      .with('user')
      .fetch()

    return result
  }

  async delete ({ params }) {
    return Page
      .query()
      .where('id', params.id)
      .with('pageCategory')
      .with('pageImage')
      .with('pageChequer')
      .with('pageBoatBrand')
      .with('pageBoatType')
      .delete()
  }

  async update ({ params, request }) {
    const result = await Page.find(params.id)
    const data = request.only([
      'country_id',
      'area_id',
      'base_id',
      'title',
      'subtitle',
      'status',
      'keyword',
      'slug',
      'description',
      'body',
      'locale',
      'search_description',
      'slug',
      'template',
      'type',
      'user_id',
      'created_at',
      'updated_at',
      'pageBoatBrand',
      'pageBoatType',
      'pageCategory',
      'pageChequer',
      'pageImage'
    ])

    const pageImagesData = (data.pageImage) ? data.pageImage : []
    const pageChequersData = (data.pageChequer) ? data.pageChequer : []
    const pageCategoriesData = (data.pageCategory) ? data.pageCategory : []
    const pageBoatBrandData = (data.pageBoatBrand) ? data.pageBoatBrand : []
    const pageBoatTypeData = (data.pageBoatType) ? data.pageBoatType : []

    let pageData = data
    _.unset(pageData, 'pageImage')
    _.unset(pageData, 'pageChequer')
    _.unset(pageData, 'pageCategory')
    _.unset(pageData, 'pageBoatType')
    _.unset(pageData, 'pageBoatBrand')
    _.merge(result, pageData)

    await result.save()

    for (const value of pageImagesData) {
      if (value.hasOwnProperty('page_id') && value.page_id > 0) { // update the image
        await Database
          .table('page_images')
          .where('page_id', value.page_id)
          .where('fileName', value.fileName)
          .update(value)
      } else if (value.hasOwnProperty('page_id') && value.page_id < 0) { // delete the image
        await Database
          .table('page_images')
          .where('page_id', Math.abs(value.page_id))
          .where('fileName', value.fileName)
          .delete()
      } else { // create a new image
        await result.pageImage().create(value)
      }
    }

    if (pageData.template === 'chequer') {
      for (const i in pageChequersData) {
        pageChequersData[i].order = parseInt(i) + 1
        if (pageChequersData[i].hasOwnProperty('id') && pageChequersData[i].id > 0) {
          await result.pageChequer().where('id', pageChequersData[i].id).update(pageChequersData[i])
        } else if (pageChequersData[i].hasOwnProperty('id') && pageChequersData[i].id < 0) {
          await result.pageChequer().where('id', Math.abs(pageChequersData[i].id)).delete()
        } else {
          await result.pageChequer().create(pageChequersData[i])
        }
      }
    }

    for (const value of pageBoatBrandData) {
      if (value.hasOwnProperty('page_id') && value.brand_id < 0) {
        await result.pageBoatBrand().where('brand_id', Math.abs(value.brand_id)).delete()
      } else if (!value.hasOwnProperty('page_id')) {
        await result.pageBoatBrand().create(value)
      }
    }

    for (const value of pageBoatTypeData) {
      if (value.hasOwnProperty('page_id') && value.type_id < 0) {
        await result.pageBoatType().where('type_id', Math.abs(value.type_id)).delete()
      } else if (!value.hasOwnProperty('page_id')) {
        await result.pageBoatType().create(value)
      }
    }

    for (const value of pageCategoriesData) {
      if (value.hasOwnProperty('page_id') && value.category_id < 0) {
        await result.pageCategory().where('category_id', Math.abs(value.category_id)).delete()
      } else if (!value.hasOwnProperty('page_id')) {
        await result.pageCategory().create(value)
      }
    }

    return Page
      .query()
      .where('id', params.id)
      .with('pageCategory')
      .with('pageImage')
      .with('pageChequer', (builder) => {
        builder.orderBy('order', 'asc')
      })
      .with('pageBoatBrand')
      .with('pageBoatType')
      .with('user')
      .first()
  }

  async store ({ request, auth }) {
    const result = new Page()
    const data = request.only([
      'country_id',
      'area_id',
      'base_id',
      'title',
      'keyword',
      'status',
      'slug',
      'subtitle',
      'description',
      'body',
      'locale',
      'search_description',
      'slug',
      'template',
      'type',
      'user_id',
      'created_at',
      'updated_at',
      'pageBoatBrand',
      'pageBoatType',
      'pageCategory',
      'pageChequer',
      'pageImage'
    ])

    const user = auth.user

    const pageImagesData = (data.pageImage) ? data.pageImage : []
    const pageChequersData = (data.pageChequer) ? _.map(data.pageChequer, (p, i) => {
      p.order = i
      return p
    }) : []
    const pageCategoriesData = (data.pageCategory) ? data.pageCategory : []
    const pageBoatBrandData = (data.pageBoatBrand) ? data.pageBoatBrand : []
    const pageBoatTypeData = (data.pageBoatType) ? data.pageBoatType : []

    let pageData = data
    pageData.user_id = user.id

    _.unset(pageData, 'pageImage')
    _.unset(pageData, 'pageChequer')
    _.unset(pageData, 'pageCategory')
    _.unset(pageData, 'pageBoatType')
    _.unset(pageData, 'pageBoatBrand')
    _.merge(result, pageData)

    await result.save()

    result.pageImage = await result.pageImage().createMany(pageImagesData)

    if (pageChequersData && pageData.template === 'chequer') {
      result.pageChequer = await result.pageChequer().createMany(pageChequersData)
    }
    result.pageCategory = await result.pageCategory().createMany(pageCategoriesData)
    result.pageBoatType = await result.pageBoatType().createMany(pageBoatTypeData)
    result.pageBoatBrand = await result.pageBoatBrand().createMany(pageBoatBrandData)
    result.user = user
    return result
  }

  async destinations () {
    let data = await Database
      .from('v1_regions_with_boats')
      .select('*')
      .whereIn('country_name_en', ['croatia', 'greece', 'british virgin islands'])
    data = Destinations.sortDestinationsToArray(data, 'sailing-itineraries')
    return data
  }

  async countries () {
    return Database.select('*')
      .from('countries')
      .groupBy('countries.ID')
      .orderBy('countries.Popular')
  }

  async upload ({ request, params }) {
    let config = Config.get('sailchecker')
    let fileName = ''
    request.multipart.file('file', {}, async (file) => {
      fileName = file._clientName
      // const roundedCorners = new Buffer(
      //   '<svg><text style="fill:black;font-family:Arial;font-size:20px;" width="155px" height="24px" x="20" y="20" id="e1_texte" >SailChecker</text></svg>'
      // );

      const s3Params = {
        'ContentType': 'image/jpeg',
        'ACL': 'public-read'
      }

      const imageOptions = {
        quality: 85
      }

      const thumbnail =
        sharp()
          .resize(200, 200)
          .jpeg(imageOptions)

      const medium =
        sharp()
          .resize(600, 400)
          .jpeg(imageOptions)

      const chequer =
        sharp()
          .resize(800, 800)
          .jpeg(imageOptions)

      const original =
        sharp()
          .resize(1600, 800)
          .jpeg(imageOptions)

      try {
        const bucket = (params.bucket) ? `uploads/${params.bucket}/` : config.s3.yachtCharterContent

        await Promise.all([
          Drive.put(bucket + file._clientName, file.stream.pipe(original), s3Params),
          Drive.put(bucket + 'chequer-' + file._clientName, file.stream.pipe(chequer), s3Params),
          Drive.put(bucket + 'medium-' + file._clientName, file.stream.pipe(medium), s3Params),
          Drive.put(bucket + 'thumbnail-' + file._clientName, file.stream.pipe(thumbnail), s3Params)
        ])
      } catch (e) {
        console.log(e)
      }
    })

    await request.multipart.process()
    return {
      fileName: fileName
    }
  }
}

module.exports = ContentController
