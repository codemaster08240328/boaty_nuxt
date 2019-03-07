'use strict'

require('dotenv').config()
const axios = require('axios')

const title = 'Sailing Holidays and Yacht Charter experts'

const whyus = 'Booking a Sailing Holiday or Yacht Charter is easy with SailChecker.com. We blend the latest search technology with service for unrivalled service and value ▶ 13,000+ Crewed Yachts and Bareboat Charters Worldwide - Sailboats ✔ Motor Yachts ✔ Catamarans ✔'

const image = 'https://s3.eu-west-2.amazonaws.com/sc30/static/sailchecker_marina_sunset.jpg'

// "openingHours": "Mo,Tu,We,Th,Fr,Sa,Su 09:00-17:00",
// "geo": {
//   "@type": "GeoCoordinates",
//   "latitude": "51.52",
//   "longitude": "-0.09"
// },

let appldjson = `{
  "@context": "http://schema.org",
  "@type": "Organization",
  "@id": "https://sailchecker.com/#header",
  "url": "https://sailchecker.com",
  "name": "SailChecker",
  "description": "${whyus}", 
  "logo": "https://s3.eu-west-2.amazonaws.com/sc30/static/sailchecker_logo_icon_only.svg",
  "telephone": "+44 800 988 118",
  "sameAs" : [
    "https://www.facebook.com/sailChecker1/",
    "https://twitter.com/sailchecker",
    "https://www.instagram.com/sailchecker/",
    "https://plus.google.com/+Sailchecker",
    "https://www.pinterest.com/drakesail/sail-checker/"
  ]
}`
const jivo = `(function(){ var widget_id = 'gY82g8xeKp';var d=document;var w=window;function l(){ var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/geo-widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();`

const fb = `(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.12&appId=1301570169959475&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));`

const zadarama = `
var ZCallbackWidgetLinkId  = '39b93f36e9c9aa9b19b0ec19abbc6b74';
var ZCallbackWidgetDomain  = 'my.zadarma.com';
(function(){
    var lt = document.createElement('script');
   lt.type ='text/javascript';
   lt.charset = 'utf-8';
   lt.async = true;
   lt.src = 'https://' + ZCallbackWidgetDomain + '/callbackWidget/js/main.min.js';
   var sc = document.getElementsByTagName('script')[0];
   if (sc) sc.parentNode.insertBefore(lt, sc);
   else document.documentElement.firstChild.appendChild(lt);
})();`

module.exports = {
  head: {
    htmlAttrs: {
      lang: 'en'
    },
    title: title,
    titleTemplate: '%s - SailChecker',
    script: [
      { innerHTML: appldjson, type: 'application/ld+json' },
      { innerHTML: jivo, type: 'text/javascript' },
      { innerHTML: zadarama, type: 'text/javascript' },
      { innerHTML: fb, type: 'text/javascript', body: true }
      // { src: 'https://s3.eu-west-2.amazonaws.com/sc30/uploads/json/rates.js', type: 'text/javascript' },
      // { src: 'https://cdnjs.cloudflare.com/ajax/libs/money.js/0.2.0/money.js', type: 'text/javascript' }
    ],
    __dangerouslyDisableSanitizers: ['script'],
    meta: [
      {
        charset: 'utf-8'
      },
      {
        name: 'viewport',
        content: 'width=device-width, initial-scale=1'
      },
      { hid: 'description', name: 'description', content: whyus },
      { hid: 'twittertitle', name: 'twitter:title', content: title },
      { hid: 'twitterdescription', name: 'twitter:description', content: whyus },
      { hid: 'twittercreator', name: 'twitter:creator', content: 'Chris Lait' },
      { hid: 'twitterimg', name: 'twitter:image:src', content: image },
      { hid: 'ogtitle', property: 'og:title', content: title },
      { hid: 'ogtype', property: 'og:type', content: 'website' },
      { hid: 'ogurl', property: 'og:url', content: process.env.BASE_URL },
      { hid: 'ogimage', property: 'og:image', content: image },
      { hid: 'ogdescription', property: 'og:description', content: whyus },
      { hid: 'ogsitename', property: 'og:site_name', content: title },
      { hid: 'twittercard', name: 'twitter:card', content: 'summary_large_image' },
      { hid: 'twittersite', name: 'twitter:site', content: '@SailChecker' }
    ],
    link: [
      {
        rel: 'icon',
        type: 'image/x-icon',
        href: 'https://s3.eu-west-2.amazonaws.com/sc30/static/favicon.ico'
      },
      {
        rel: 'stylesheet',
        href: 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons'
      },
      {
        rel: 'stylesheet',
        href: 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'
      },
      {
        rel: 'stylesheet',
        href: 'https://cdnjs.cloudflare.com/ajax/libs/vuetify/1.0.6/vuetify.min.css'
      }
    ]
  },
  build: {
    vendor: ['axios', 'vuetify'],
    babel: {
      presets: ['vue-app']
    },
    /*
    ** Run ESLint on save
    */
    extend (config, { isDev, isClient }) {
      if (isDev && isClient) {
        config.module.rules.push({
          enforce: 'pre',
          test: /\.(js|vue)$/,
          loader: 'eslint-loader',
          exclude: /(node_modules)/
        })
      }
    }
    // extractCSS: true
  },
  router: {
    middleware: ['i18n', 'mobile', 'breadcrumbs', 'axios', 'gclid'],
    extendRoutes (routes, resolve) {
      // use unshift to add to the end of the routes as nuxtjs adds /:lang/ route after and that messes with vue-router
      routes.unshift(
        {
          name: 'preview-catamaran-charters-cat',
          path: '/preview/catamaran-charter/',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'preview-catamaran-charters-slug',
          path: '/preview/catamaran-charter/:slug',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'crewed-charters-cat',
          path: '/crewed-charter/',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        }, {
          name: 'lang-crewed-charters-cat',
          path: '/:lang/crewed-charter/',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'crewed-charters-slug',
          path: '/crewed-charter/:slug',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'lang-crewed-charters-slug',
          path: '/:lang/crewed-charter/:slug',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'catamaran-charters-cat',
          path: '/catamaran-charter/',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'lang-catamaran-charters-cat',
          path: '/:lang/catamaran-charter/',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'catamaran-charters-slug',
          path: '/catamaran-charter/:slug',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        }, {
          name: 'lang-catamaran-charters-slug',
          path: '/:lang/catamaran-charter/:slug',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'bareboat-charters-cat',
          path: '/bareboat-charter/',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'lang-bareboat-charters-cat',
          path: '/:lang/bareboat-charter/',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'bareboat-charters-slug',
          path: '/bareboat-charter/:slug',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        }, {
          name: 'lang-bareboat-charters-slug',
          path: '/:lang/bareboat-charter/:slug',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'luxury-yacht-charters-cat',
          path: '/luxury-yacht-charter/',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'lang-luxury-yacht-charters-cat',
          path: '/:lang/luxury-yacht-charter/',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'luxury-yacht-charters-slug',
          path: '/luxury-yacht-charter/:slug',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        }, {
          name: 'lang-luxury-yacht-charters-slug',
          path: '/:lang/luxury-yacht-charter/:slug',
          component: resolve(__dirname, 'pages/_lang/preview/products/index.vue')
        },
        {
          name: 'sailing-itineraries-cat',
          path: '/sailing-itineraries/',
          component: resolve(__dirname, 'pages/_lang/preview/sailing-itineraries/index.vue')
        }, {
          name: 'lang-sailing-itineraries-cat',
          path: '/:lang/sailing-itineraries/',
          component: resolve(__dirname, 'pages/_lang/preview/sailing-itineraries/index.vue')
        },
        {
          name: 'sailing-itineraries-slug',
          path: '/sailing-itineraries/:slug',
          component: resolve(__dirname, 'pages/_lang/preview/sailing-itineraries/_slug/index.vue')
        }, {
          name: 'lang-sailing-itineraries-slug',
          path: '/:lang/sailing-itineraries/:slug',
          component: resolve(__dirname, 'pages/_lang/preview/sailing-itineraries/_slug/index.vue')
        },
        // // blogs preview
        // {
        //   name: 'blog-cat',
        //   path: '/blog',
        //   component: resolve(__dirname, 'pages/_lang/preview/sailing-itineraries/index.vue')
        // }, {
        //   name: 'lang-blog-cat',
        //   path: '/:lang/preview/blog/',
        //   component: resolve(__dirname, 'pages/_lang/preview/sailing-itineraries/index.vue')
        // },
        // previews
        {
          name: 'blog-slug',
          path: '/preview/blog/:slug',
          component: resolve(__dirname, 'pages/_lang/preview/sailing-itineraries/_slug/index.vue')
        },
        {
          name: 'yachtcharter-country-slug',
          path: '/preview/yacht-charter/:country',
          component: resolve(__dirname, 'pages/_lang/yacht-charter/_country/_area/_base/index.vue')
        },
        {
          name: 'yachtcharter-area-slug',
          path: '/preview/yacht-charter/:country/:area',
          component: resolve(__dirname, 'pages/_lang/yacht-charter/_country/_area/_base/index.vue')
        },
        {
          name: 'yachtcharter-base-slug',
          path: '/preview/yacht-charter/:country/:area/:base',
          component: resolve(__dirname, 'pages/_lang/yacht-charter/_country/_area/_base/index.vue')
        },
        // lang previews
        {
          name: 'lang-blog-slug',
          path: '/:lang/preview/blog/:slug',
          component: resolve(__dirname, 'pages/_lang/preview/sailing-itineraries/_slug/index.vue')
        },
        {
          name: 'lang-yachtcharter-country-slug',
          path: '/:lang/preview/yacht-charter/:country',
          component: resolve(__dirname, 'pages/_lang/yacht-charter/_country/_area/_base/index.vue')
        },
        {
          name: 'lang-yachtcharter-area-slug',
          path: '/:lang/preview/yacht-charter/:country/:area',
          component: resolve(__dirname, 'pages/_lang/yacht-charter/_country/_area/_base/index.vue')
        },
        {
          name: 'lang-yachtcharter-base-slug',
          path: '/:lang/preview/yacht-charter/:country/:area/:base',
          component: resolve(__dirname, 'pages/_lang/yacht-charter/_country/_area/_base/index.vue')
        }
      )
    }
  },
  plugins: [
    { src: '~/plugins/i18n' },
    { src: '~/plugins/vuetify.js' },
    { src: '~/plugins/axios' },
    { src: '~/plugins/vue-cookie.js' },
    { src: '~/plugins/googlemaps.js' },
    { src: '~/plugins/interaction.js' },
    { src: '~/plugins/stars.js', ssr: false },
    { src: '~/plugins/social.js', ssr: false }
  ],
  /*
  ** Global CSS
  */
  css: [
    '~/assets/css/main.css'
  ],
  /*
    import required plugins
  */
  /*
  ** Customize the progress-bar color
  */
  loading: { color: '#FFFFFF' },
  /*
  ** Point to resources
  */
  srcDir: __dirname,
  modules: [
    'nuxtjs-dotenv-module',
    '@nuxtjs/sitemap',
    //  '@nuxtjs/auth',
    ['@nuxtjs/google-tag-manager', {
      id: 'GTM-MZCT9W',
      pageTracking: true
    }]
  ],
  env: {
    apiBaseUrl: process.env.API_BASE_URL,
    baseUrl: process.env.BASE_URL,
    googleMaps: process.env.GOOGLE_MAPS,
    s3: {
      url: 'https://s3.eu-west-2.amazonaws.com/sc30/',
      yachtCharterContent: 'uploads/yacht-charter/',
      sailingItineraryContent: 'uploads/sailing-itinerary/'
    }
  },
  sitemap: {
    path: '/sitemap.xml',
    hostname: process.env.BASE_URL,
    exclude: [
      '/boat',
      '/yacht-charter',
      '/search',
      '/preview/**'
    ],
    routes: async () => {
      // hold all the routes
      let routes = []
      routes.push({
        url: `${process.env.BASE_URL}sitemap-posttype-post-p1.xml`,
        priority: 0.5,
        links: [
          { lang: 'en', url: `${process.env.BASE_URL}sitemap-posttype-post-p1.xml` }
        ]
      })

      routes.push({
        url: `${process.env.BASE_URL}sitemap-posttype-page-p1.xml`,
        priority: 0.7,
        links: [
          { lang: 'en', url: `${process.env.BASE_URL}sitemap-posttype-page-p1.xml` }
        ]
      })

      let s = {
        url: '/search/',
        priority: 0.3,
        links: [
          // { lang: 'en-gb', url: process.env.BASE_URL + 'gb/search/' },
          { lang: 'en', url: process.env.BASE_URL.slice(-1).concat('/search/') }
        ]
      }
      routes.push(s)
      let b = {
        url: '/boat/',
        priority: 0.3,
        links: [
          // { lang: 'en-gb', url: process.env.BASE_URL + 'gb/boat/' },
          { lang: 'en', url: process.env.BASE_URL.slice(-1).concat('/boat/') }
        ]
      }
      routes.push(b)

      let yc = {
        url: '/yacht-charter/',
        priority: 0.3,
        links: [
          // { lang: 'en-gb', url: process.env.BASE_URL + 'gb/yacht-charter/' },
          { lang: 'en', url: process.env.BASE_URL.slice(-1).concat('/yacht-charter/') }
        ]
      }
      routes.push(yc)

      let cat = {
        url: '/catamaran-charter/',
        priority: 0.3,
        links: [
          // { lang: 'en-gb', url: process.env.BASE_URL + 'gb/catamaran-charter/' },
          { lang: 'en', url: process.env.BASE_URL.slice(-1).concat('/catamaran-charter/') }
        ]
      }
      routes.push(cat)
      let bare = {
        url: '/bareboat-charter/',
        priority: 0.3,
        links: [
          // { lang: 'en-gb', url: process.env.BASE_URL + 'gb/bareboat-charter/' },
          { lang: 'en', url: process.env.BASE_URL.slice(-1).concat('/bareboat-charter/') }
        ]
      }
      routes.push(bare)

      let luxuryyc = {
        url: '/luxury-yacht-charter/',
        priority: 0.3,
        links: [
          // { lang: 'en-gb', url: process.env.BASE_URL + 'gb/luxury-yacht-charter/' },
          { lang: 'en', url: process.env.BASE_URL.slice(-1).concat('/luxury-yacht-charter/') }
        ]
      }
      routes.push(luxuryyc)
      // generate yacht charter routes
      try {
        let { data } = await axios.get(process.env.API_BASE_URL.slice(-1).concat('/api/v1/seo/destinations'))

        for (let country of data) {
          let c = {
            url: country.url,
            changefreq: 'daily',
            priority: 1,
            links: [
              // { lang: 'en-gb', url: process.env.BASE_URL + 'gb' + country.url },
              { lang: 'en', url: process.env.BASE_URL.slice(-1).concat(country.url) }
            ]
          }
          routes.push(c)
          for (let area of country.areas) {
            let a = {
              url: area.url,
              changefreq: 'weekly',
              priority: 0.7,
              links: [
                // { lang: 'en-gb', url: process.env.BASE_URL + 'gb' + area.url },
                { lang: 'en', url: process.env.BASE_URL.slice(-1).concat(area.url) }
              ]
            }
            routes.push(a)
            for (let base of area.bases) {
              let b = {
                url: base.url,
                changefreq: 'weekly',
                priority: 0.5,
                links: [
                  // { lang: 'en-gb', url: process.env.BASE_URL + 'gb' + base.url },
                  { lang: 'en', url: process.env.BASE_URL.slice(-1).concat(base.url) }
                ]
              }
              routes.push(b)
            }
          }
        }
      } catch (e) {
        return e
      }

      // generate boat routes
      try {
        let { data } = await axios.get(process.env.API_BASE_URL.slice(-1).concat('/api/v1/seo/boats'))
        for (let boat of data) {
          let b = {
            url: boat,
            priority: 0.5,
            links: [
              // { lang: 'en-gb', url: process.env.BASE_URL + 'gb' + boat },
              { lang: 'en', url: process.env.BASE_URL.slice(-1).concat(boat) }
            ]
          }
          routes.push(b)
        }
      } catch (e) {
        return e
      }

      // generate search routes
      try {
        let { data } = await axios.get(process.env.API_BASE_URL.slice(-1).concat('/api/v1/seo/search'))

        for (let country of data) {
          let c = {
            url: country.url,
            changefreq: 'daily',
            priority: 1,
            links: [
              // { lang: 'en-gb', url: process.env.BASE_URL + 'gb' + country.url },
              { lang: 'en', url: process.env.BASE_URL.slice(-1).concat(country.url) }
            ]
          }
          routes.push(c)
          for (let area of country.areas) {
            let a = {
              url: area.url,
              changefreq: 'weekly',
              priority: 0.7,
              links: [
                // { lang: 'en-gb', url: process.env.BASE_URL + 'gb' + area.url },
                { lang: 'en', url: process.env.BASE_URL.slice(-1).concat(area.url) }
              ]
            }
            routes.push(a)
            for (let base of area.bases) {
              let b = {
                url: base.url,
                changefreq: 'weekly',
                priority: 0.5,
                links: [
                  // { lang: 'en-gb', url: process.env.BASE_URL + 'gb' + base.url },
                  { lang: 'en', url: process.env.BASE_URL.slice(-1).concat(base.url) }
                ]
              }
              routes.push(b)
            }
          }
        }
      } catch (e) {
        return e
      }

      return routes
    }
  }
}
