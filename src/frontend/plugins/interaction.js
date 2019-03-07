import Cookie from 'js-cookie'

export default ({ req, app, store }, inject) => {
  const interaction = {
    setDataInCookies ({ name = '', data = {} }) {
      const dataKeys = Object.keys(data)

      console.log('setting data', data)
      const cookie = Cookie.getJSON(name) || {}
      for (const key of dataKeys) {
        if (data[key] === false || data[key] === null) {
          delete cookie[key]
        } else {
          cookie[key] = data[key]
        }
      }
      console.log('setting data to cookie', cookie)
      cookie.version = 4 // sets cookie version, invalidate this when changing cookie structure
      Cookie.set(name, JSON.stringify(cookie))
    },
    getDataFromCookie ({ name = '', json = true }) {
      if (process.client) {
        if (json) {
          return Cookie.getJSON(name) || false
        } else {
          return Cookie.get(name)
        }
      } else if (process.server) {
        const cookies = (req.headers.cookie) ? req.headers.cookie : false

        if (cookies && cookies.includes(name + '=')) {
          if (json) {
            return JSON.parse(decodeURIComponent(cookies.split(`${name}=`)[1].split(';')[0])) || false
          } else {
            return decodeURIComponent(cookies.split(`${name}=`)[1].split(';')[0]) || false
          }
        }
      }

      return false
    }
  }

  inject('interaction', interaction)
}
