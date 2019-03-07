// import { each, map } from 'lodash'

export default function ({ isHMR, app, store, route, params, query, error, redirect }) {
  const defaultLocale = app.i18n.fallbackLocale
  if (isHMR) return

  // Get locale from params
  let locale = params.lang || defaultLocale

  const shortLocale = [
    'gb'
  ]
  console.log('i18n', params.lang, locale)
  if (store.state.locales.indexOf(locale) === -1 && shortLocale.indexOf(locale) === -1) {
    return error({ message: 'This page could not be found.', statusCode: 404 })
  }

  locale = (locale === 'gb') ? 'en-gb' : locale

  // Set locale
  store.commit('setLang', locale)
  app.i18n.locale = store.state.locale
}
