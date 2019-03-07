'use strict'
const _ = use('lodash')

class LocaleDetector {
  async handle ({ request, response }, next) {
    const supportedLocales = [
      { locale: 'en-us', lang: 'en' },
      { locale: 'en-gb', lang: 'en' }
    ]

    const defaultLocale = 'en-us'
    const defaultLang = 'en'

    if (request.request.headers['accept-language']) {
      const dynamicLocale = request.request.headers['accept-language'].split(',')[0]
      const dynamicLang = request.request.headers['accept-language'].split(',')[0].split('-')[0]

      const found = _.findIndex(supportedLocales, (o) => {
        return o.locale.toLowerCase() === dynamicLocale.toLowerCase()
      })
      request.locale = (found !== -1) ? dynamicLocale : defaultLocale
      request.lang = (found !== -1) ? dynamicLang : defaultLang
    } else {
      request.locale = defaultLocale
      request.lang = defaultLang
    }

    // call next to advance the request
    await next()
  }
}

module.exports = LocaleDetector
