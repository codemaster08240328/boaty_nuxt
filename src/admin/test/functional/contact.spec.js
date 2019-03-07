'use strict'

const { test, trait } = use('Test/Suite')('Contact')
const User = use('App/Models/User')
/**
 *  Tests the routes relating to boat pages
 */
trait('Test/ApiClient')
trait('Auth/Client')

test('Create & update a contact', async ({ assert, client }) => {
  const data = {
    email: 'matt@saowapan.com',
    name: 'Matt Gould'
  }

  const response = await client.post('api/v1/contact/store').send(data).end()
  response.assertStatus(200)
  response.assertHeader('content-type', 'application/json; charset=utf-8')

  const response1 = await client.post(`api/v1/contact/update/${response.body.confirm_code}`).send({
    phone_number: '8888-888-8888'
  }).end()
  response1.assertStatus(200)
  response1.assertHeader('content-type', 'application/json; charset=utf-8')

  const response2 = await client.post(`api/v1/contact/update/${response.body.confirm_code}`).send({
    adults: 2,
    children: 2,
    message: 'Hello World'
  }).end()
  response2.assertStatus(200)
  response2.assertHeader('content-type', 'application/json; charset=utf-8')
})

test('List contacts', async ({ assert, client }) => {
  const user = await User.find(3)

  const response = await client
    .get('api/v1/sc-secret-admin/bookings')
    .loginVia(user, 'jwt')
    .end()

  response.assertStatus(200)
  response.assertHeader('content-type', 'application/json; charset=utf-8')
  assert.hasAllKeys(response.body[0], [
    'id',
    'boat_id',
    'base_id',
    'user_id',
    'rep_id',
    'name',
    'email',
    'adults',
    'children',
    'phone_number',
    'message',
    'currency',
    'confirm_code',
    'ip',
    'country',
    'region',
    'city',
    'device',
    'browser',
    'date',
    'weeks',
    'boat_price_euro',
    'exchange_rate',
    'user_agent',
    'updated_at',
    'created_at',
    'discount',
    'status',
    'BrandName',
    'ModelName',
    'Name',
    'url'
  ])
})
