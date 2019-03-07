'use strict'

const resolve = require('path').resolve
require('dotenv').config()

module.exports = {
  /*
  ** Headers of the page
  */
  head: {
    title: 'SailChecker',
    meta: [
      {
        charset: 'utf-8'
      },
      {
        name: 'viewport',
        content: 'width=device-width, initial-scale=1'
      },
      {
        hid: 'description',
        name: 'description',
        content: 'SailChecker'
      }
    ],
    link: [
      {
        rel: 'icon',
        type: 'image/x-icon',
        href: '/favicon.ico'
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
        href: 'https://cdnjs.cloudflare.com/ajax/libs/cropper/3.1.6/cropper.min.css'
      }
    ]
  },
  // build: {
  //   vendor: ['axios']
  // },
  router: {
    // middleware: ['i18n'],
    base: '/sc-secret-admin-cms'
  },
  plugins: [
    // { src: '~/plugins/i18n' },
    { src: '~/plugins/vuetify.js' },
    { src: '~/plugins/axios' },
    { src: '~/plugins/vue-cookie.js' },
    { src: '~/plugins/quill.js', ssr: false },
    { src: '~/plugins/upload.js', ssr: false }
  ],
  /*
  ** Global CSS
  */
  css: [
    '~assets/css/main.css',
    resolve(__dirname, '..', 'node_modules/vuetify/dist/vuetify.min.css'),
    resolve(__dirname, '..', 'node_modules/quill/dist/quill.snow.css'),
    resolve(__dirname, '..', 'node_modules/quill/dist/quill.bubble.css'),
    resolve(__dirname, '..', 'node_modules/quill/dist/quill.core.css')
  ],
  /*
    import required plugins
  */
  /*
  ** Customize the progress-bar color
  */
  loading: { color: '#744d82' },
  /*
  ** Point to resources
  */
  srcDir: resolve(__dirname, '..', 'resources'),
  modules: [ 'nuxtjs-dotenv-module' ],
  env: {
    apiBaseUrl: process.env.API_BASE_URL
  }
}
