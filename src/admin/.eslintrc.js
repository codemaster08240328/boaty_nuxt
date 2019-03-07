'use strict'

// module.exports = {

//   root: true,

//   parser: 'babel-eslint',

//   env: {
//     browser: true,
//     node: true
//   },

//   extends: 'standard',

//   plugins: [
//     'html'
//   ],

//   rules: {
//     'arrow-parens': 0,
//     'generator-star-spacing': 0,
//     'no-debugger': process.env.NODE_ENV === 'production' ? 2 : 0,
//     'no-console': 2
//   },

//   globals: {
//     'use': true
//   }

// }


module.exports = {
  root: true,
  parser: 'babel-eslint',
  env: {
    browser: true,
    node: true
  },
  extends: 'standard',
  // required to lint *.vue files
  plugins: [
    'html'
  ],
  // add your custom rules here
  rules: {},
  globals: {
    'use': true
  }
}
