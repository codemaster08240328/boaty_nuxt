// import { resolve } from 'path'
// import { Nuxt, Builder } from 'nuxt'
import { JSDOM } from 'jsdom'
import test from 'ava'
import ApiClient from 'js-api-client'

const client = new ApiClient({
  baseURL: 'http://nuxtjs-frontend:3333'
})

// Init Nuxt.js and create a server listening on localhost:4000
test.before(async () => {

}, 30000)

// Example of testing only generated html
test('Route / exits and render HTML', async t => {
  const { data } = await client.get(`/`)
  const { window } = new JSDOM(data).window
  const element = window.document.getElementsByTagName('h1')[0]
  t.not(element, null)
  t.is(element.textContent, 'Crewed Yachts and Bareboat Charters Worldwide')
  t.is(element.className, 'white--text')
  t.is(window.getComputedStyle(element).color, '#fff')
})

// Close server and ask nuxt to stop listening to file changes
test.after('Closing server and nuxt.js', t => {

})
