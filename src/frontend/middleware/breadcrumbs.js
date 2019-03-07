export default (context) => {
  const basePath = context.env.baseUrl.slice(0, -1)

  /*
    Breadcrumbs will always start with / or /us for e.g.
  */
  let breadcrumbs = [{
    '@id': `${basePath}${context.app.i18n.path('/')}`,
    name: 'Home'
  }]

  const route = context.route
  let routes = route.fullPath.split('/')
  routes.shift()
  if (routes[routes.length - 1] === '') {
    routes.pop()
  }

  for (let i in routes) {
    if (['gb'].indexOf(routes[i]) === -1) {
      let lastUrl = breadcrumbs[breadcrumbs.length - 1]['@id']
      let sep = ''
      if (lastUrl.slice(-1) === '/') {
        sep = ''
      } else {
        sep = '/'
      }

      // if croatia?querystring
      if (routes[i].indexOf('?')) {
        routes[i] = routes[i].split('?')[0]
      }

      // ?querystring
      if (routes[i].charAt(0) !== '?') {
        breadcrumbs.push({
          '@id': `${lastUrl}${sep}${routes[i]}/`,
          name: context.app.i18n.urlSectionToString(routes[i])
        })
      }
    }
  }

  context.breadcrumbs = breadcrumbs
}
