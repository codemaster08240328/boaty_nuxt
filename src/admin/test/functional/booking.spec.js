'use strict'

const { test, trait } = use('Test/Suite')('Booking')
const Booking = use('App/Models/Booking')
const geoip = require('geoip-lite')
const User = use('App/Models/User')
/**
 *  Tests the routes relating to boat pages
 */
trait('Test/ApiClient')
trait('Auth/Client')

test('Create a booking', async ({ assert, client }) => {
  const data = {
    adults: 2,
    base_id: 614,
    boat_id: 37952,
    boat_price_euro: 2200,
    children: 2,
    currency: 'eur',
    date: '2018-05-26',
    name: 'Matt Gould',
    email: 'matt@saowapan.com',
    exchange_rate: 1,
    message: 'I want to book this boat',
    phone_number: '0954422808',
    weeks: 3
  }

  const response = await client.post('api/v1/booking/store').send(data).end()
  response.assertStatus(200)
})

test('Create and list bookings', async ({ assert, client }) => {
  let booking = {
    adults: 2,
    base_id: 614,
    boat_id: 37952,
    boat_price_euro: 2200,
    children: 2,
    currency: 'eur',
    date: '2018-05-26',
    name: 'Matt Gould',
    email: 'matt@saowapan.com',
    exchange_rate: 1,
    message: 'I want to book this boat',
    phone_number: '0954422808',
    weeks: 3,
    ip: '171.7.238.181',
    user_agent: 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36'
  }

  const geo = geoip.lookup(booking.ip) // get geo ip data

  if (geo != null) {
    booking.country = geo.country
    booking.region = geo.region
    booking.city = geo.city
  }

  booking.confirm_code = 'test'
  booking.status = 1
  await Booking.create(booking)

  const user = await User.find(3)

  const response = await client
    .get('api/v1/sc-secret-admin/bookings')
    .loginVia(user, 'jwt')
    .end()

  response.assertStatus(200)

  assert.isArray(response.body)
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
