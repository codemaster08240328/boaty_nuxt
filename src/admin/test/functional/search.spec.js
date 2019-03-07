'use strict'

const { test, trait } = use('Test/Suite')('Search')
const moment = use('moment')

trait('Test/ApiClient')

test('Load /search/', async ({ client, assert }) => {
  const response = await client.get(`api/v1/pages/search`).end()

  response.assertStatus(200)
  response.assertHeader('content-type', 'application/json; charset=utf-8')
})

test('Load /search/{country}', async ({ client, assert }) => {
  const response = await client.get(`api/v1/pages/search`).send({
    country: 'croatia'
  }).end()

  response.assertStatus(200)
  response.assertHeader('content-type', 'application/json; charset=utf-8')
})

test('Use all search filters', async ({ assert, client }) => {
  const data = {
    date: moment(new Date()).add(14, 'days'),
    country: 'greece',
    type: 4,
    sortby: 5,
    cabins: [1, 2, 3],
    prices: [
      [1000, 2000],
      [2001, 4000],
      [4001, 8000]
    ],
    lengths: [
      [8, 10],
      [10, 12]
    ],
    years: [
      [2010, 2011],
      [2016, 2017],
      [2017, 2100]
    ],
    toilets: [1, 2, 3]
  }

  const response = await client.post('api/v1/search/boats/filter')
    .send(data)
    .end()
  response.assertStatus(200)
  response.assertHeader('content-type', 'application/json; charset=utf-8')
  assert.hasAllKeys(response.body, ['boats_paginate', 'boats'])
})
