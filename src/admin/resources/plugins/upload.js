import Vue from 'vue'

if (process.browser) {
  const VueUploadComponent = require('vue-upload-component')
  Vue.component('file-upload', VueUploadComponent)
}
