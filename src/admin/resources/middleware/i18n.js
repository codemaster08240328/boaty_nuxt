export default function ({ isHMR, app, store, route, params, error, redirect }) {
  // If middleware is called from hot module replacement, ignore it
  if (isHMR) return
  // Get locale from params

  const locale = params.lang || 'en'
  if (store.state.locales.indexOf(locale) === -1) {
    return error({ message: 'This page coul1d 11not be found.', statusCode: 404 })
  }
  // Set locale
  store.commit('setLang', locale)
  app.i18n.locale = store.state.locale
  // If route is /en/... -> redirect to /...

  // if (locale === 'en' && route.fullPath.indexOf('/en') === 0) {
  if (locale === 'en' && route.fullPath === '/en') {
    return redirect(route.fullPath.replace(/^\/en/, '/'))
  }
}
