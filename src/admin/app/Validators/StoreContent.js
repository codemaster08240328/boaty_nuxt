'use strict'

class StoreContent {
  get validateAll () {
    return true
  }

  get rules () {
    return {
      country_id: 'number',
      area_id: 'number',
      base_id: 'number',
      title: 'string|required',
      subtitle: 'string|required',
      slug: 'string',
      // status: 'number',
      description: 'string|required',
      body: 'string|required',
      locale: 'string|required',
      search_description: 'string',
      slug: 'string',
      template: 'string|required',
      type: 'string|required',
      user_id: 'number',
      created_at: 'date',
      updated_at: 'date',
      'pageImage.*.altText': 'string|required',
      'pageImage.*.titleText': 'string|required',
      'pageImage.*.fileName': 'string|required',
      'pageChequer.*.altText': 'string|required',
      'pageChequer.*.titleText': 'string|required',
      'pageChequer.*.title': 'string|required',
      'pageChequer.*.body': 'string|required',
      // 'pageChequer.*.order': 'number|required',
      'pageCategory.*.category_id': 'number|required',
      'pageBoatType.*.type_id': 'number|required',
      'pageBoatBrand.*.brand_id': 'number|required'
    }
  }

  get sanitizationRules () {
    return {
      country_id: 'to_int',
      area_id: 'to_int',
      base_id: 'to_int',
      user_id: 'to_int',
      'pageChequer.*.order': 'to_int',
      'pageCategory.*.category_id': 'to_int',
      'pageBoatType.*.type_id': 'to_int',
      'pageBoatBrand.*.brand_id': 'to_int'
    }
  }

  async fails (errorMessages) {
    return this.ctx.response.status(422).send(errorMessages)
  }
}

module.exports = StoreContent
