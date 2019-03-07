import Vue from 'vue'

// import Quill from 'quill'
import VueQuillEditor from 'vue-quill-editor/ssr'

if (process.browser) {
  // Quill.register('modules/counter', (quill, options) => {
  //   const id = quill.container.getAttribute('id')
  //   const count = document.createElement('div')
  //   const idName = `class${id}`

  //   count.setAttribute('id', idName)
  //   count.innerHTML = '0 characters'

  //   quill.container.appendChild(count)

  //   quill.on('text-change', () => {
  //     const text = quill.getLength()
  //     const characters = text.length - 1 + ' charcters'
  //     const count = document.getElementById(idName)
  //     count.innerHTML = characters
  //   })
  // })
  Vue.use(VueQuillEditor)
}
