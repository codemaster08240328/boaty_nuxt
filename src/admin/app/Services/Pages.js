'use strict'
const Page = use('App/Models/Page')

class Pages {
  /**
   * @description retrieves a list of pages to be used as cards on frontend - typically on boat/search/pages
   * 
   * @param {any} { type = false, countryId = false } 
   * @returns 
   * @memberof Pages
   */
  async cards ({ type = false, countryId = false, limit = 4 }) {
    const pages = Page
      .query()
      .setVisible(['id', 'title', 'description', 'link'])
      .with('user', (builder) => {
        builder.setVisible(['fullName'])
      })
      .with('country')
      .with('pageImage', (builder) => {
        builder.setVisible(['fileName', 'altText', 'titleText'])
      })
      .orderBy('created_at', 'desc')

    if (type) {
      pages.where('type', type)
    }

    if (countryId) {
      pages.where('country_id', countryId)
    }

    return pages.limit(limit).fetch()
  }
}

module.exports = new Pages()
