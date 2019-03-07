import Vue from 'vue'
const HelloJs = require('hellojs/dist/hello.all.min.js')
const VueHello = require('vue-hellojs')

HelloJs.init({
  facebook: '1661461583903104'
}, {
  redirect_uri: 'https://sailchecker.dev/boat/test'
})

Vue.use(VueHello, HelloJs)
