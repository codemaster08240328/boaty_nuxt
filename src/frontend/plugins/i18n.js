import Vue from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

export default ({ app, store }) => {
  // Set i18n instance on app
  // This way we can use it in middleware and pages asyncData/fetch
  /*
    Set i18n instance on app
    We can now use i18n in middleware and pages asyncdata/fetch
  */
  app.i18n = new VueI18n({
    locale: store.state.locale,
    fallbackLocale: 'en-us',
    messages: {
      'en-gb': require('~/locales/en-gb.json'),
      'en-us': require('~/locales/en-us.json')
    }
  })

  /*
    Returns the a url path in the current locale

    /yacht-charter/ > /gb/yacht-charter/
  */
  app.i18n.path = (link) => {
    // bit of a safety cushion as this was added several months after project began, urls should contain the / by default
    if (link.charAt(0) === '/') {
      link = link.slice(1)
    }

    if (app.i18n.locale === app.i18n.fallbackLocale) {
      return `/${link}`
    }

    return `/${app.i18n.locale.split('-')[1]}/${link}`
  }

  /*
    Strips out the locale e.g.

    /gb/yacht-charter/ > /yacht-charter/
  */
  app.i18n.hreflang = (path) => {
    let re = new RegExp(/(?:\/[a-z]+\/)(.+)/g)

    if (app.i18n.locale !== 'en-us') {
      const prefix = re.exec(path)
      if (prefix !== null) {
        path = '/' + prefix[1]
      }
    }
    return path.slice(1)
  }

  app.i18n.urlSectionToString = (path) => {
    const hyphenSpace = new RegExp('-', 'g')
    const underHyphen = new RegExp('_', 'g')

    return decodeURI(path.replace(hyphenSpace, ' ').replace(underHyphen, '-').replace(/\b\w/g, l => l.toUpperCase()))
  }
}
