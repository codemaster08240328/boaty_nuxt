'use strict'

const { test, trait } = use('Test/Suite')('Yacht Charter')

trait('Test/ApiClient')

test('Load a yacht charter page', async ({ assert, client }) => {
  const response = await client.get(`api/v1/pages/yachtcharter`).send({ country: 'croatia' }).end()

  response.assertStatus(200)
  response.assertHeader('content-type', 'application/json; charset=utf-8')
})
