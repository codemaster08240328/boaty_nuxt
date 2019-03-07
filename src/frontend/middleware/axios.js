export default function ({ app, route }) {
  // set axios accept-language header when routes change, as it stands navigating between locales wouldn't work without this
  app.$axios.defaults.headers['accept-language'] = route.params.lang || 'en-us'
}
