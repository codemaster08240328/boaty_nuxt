'use strict'

const { test, trait } = use('Test/Suite')('Boat')
const Boat = use('App/Models/Boat')

/**
 * 
 *  Tests the routes relating to boat pages
 * 
 */
trait('Test/ApiClient')

test('Load a boat page', async ({ assert, client }) => {
  const boat = await Boat.query().where('status', 1).first()
  const response = await client.get(`api/v1/pages/boat/${boat.ID}`).end()
  response.assertStatus(200)
  response.assertHeader('content-type', 'application/json; charset=utf-8')

  assert.hasAllKeys(response.body, ['boat', 'wp', 'pages'])

  // basic check
  assert.hasAnyKeys(response.body.boat, [
    'ID',
    'period_calendar',
    'equipment',
    'extras',
    'images',
    'boat_bases',
    'primary_base',
    'reviews'
  ])

  assert.hasAnyKeys(response.body.wp, [
    'blog',
    'last_minute'
  ])

  assert.hasAnyKeys(response.body.pages, [
    'sailingItineraries'
  ])

  assert.isObject(response.body.boat.location) // BB api sometimes returns 0 and this is null
  assert.isArray(response.body.boat.periods) // BB api sometimes has boats with no periods
})
