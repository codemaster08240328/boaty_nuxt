'use strict'

/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
| Http routes are entry points to your web application. You can create
| routes for different URL's and bind Controller actions to them.
|
| A complete guide on routing is available here.
| http://adonisjs.com/guides/routing
|
*/

const Route = use('Route')

/*
  FRONTEND ROUTES
*/

/*
  Search
*/
Route.group(() => {
  Route.get('/regions', 'SearchParamsController.regions') // used in simple search component
  Route.get('/regionsraw', 'SearchParamsController.regionsRaw')
  Route.get('/countries', 'SearchParamsController.countries')
  Route.get('/areas', 'SearchParamsController.areas')
  Route.get('/boatbrands', 'SearchParamsController.boatbrands')
  Route.get('/boattypes', 'SearchParamsController.boattypes')
  Route.post('/boats/filter', 'SearchController.getBoatsWithFilters').middleware('region') // also used on search page, for filters
}).prefix('api/v1/search')

/*
  User
*/
Route.group(() => {
  Route.post('login', 'UserController.login')
  Route.post('register', 'UserController.store')
  Route.post('register/:source', 'UserController.storeSocial').validator('UserSocialLogin')
  Route.get('auth-test', 'UserController.authTest')
}).prefix('api/v1/user')

/*
  pages aka yacht-charter
*/
Route.group(() => {
  // yacht charter, search and home require custom solutions, where as the route / encompasses our "blogs"
  Route.get('yachtcharter', 'YachtCharterController.index').middleware('region')
  Route.get('search', 'SearchController.index').middleware('region')
  Route.get('boat/:boat_id', 'BoatController.index')
  Route.get('home', 'HomeController.index')

  // blogs are in essence, the same
  Route.get('blog/:type', 'PageController.indexByType')
  Route.get('blog/:type/:slug', 'PageController.index')

  // product pages
  Route.get('products/:type/:slug?', 'ProductController.index').middleware('region')
}).prefix('api/v1/pages')

/*
  Seo
*/
Route.group(() => {
  // SITEMAP BEGIN
  Route.get('/destinations', 'SeoController.destinations') // creates an array of arrays with countries > areas > bases
  Route.get('/pages/:type', 'SeoController.pages')
  Route.get('/boats', 'SeoController.boats') // creates an array of boats
  Route.get('/search', 'SeoController.search') // creates an array of arrays with countries > areas > bases
  // SITEMAP END

  Route.get('/gmaps', 'SeoController.gmaps') // creates data for gmap markers
  Route.get('/popularlocations', 'SeoController.popularLocations')

  Route.get('/rates', 'RatesController.getRates') // maybe this shouldn't be here :)
}).prefix('api/v1/seo')

/*
  Contact/Request
*/
Route.group(() => {
  Route.post('store', 'ContactController.store').validator('StoreContact')
  Route.post('update/:confirm_code', 'ContactController.update').validator('StoreContact')
}).prefix('api/v1/contact')

/*
  Booking
*/
Route.group(() => {
  Route.post('store', 'BookingController.store')
}).prefix('api/v1/booking')

/*
  Review
*/
Route.group(() => {
  Route.post('/', 'ReviewController.store')
}).prefix('api/v1/review')

/*
  Menu
*/
Route.group(() => {
  Route.get('/', 'MenuController.index')
}).prefix('api/v1/menu')
/*
 ADMIN ROUTES
*/

/*
  Content - yacht charter pages, popular countries etc
*/
Route.group(() => {
  // uploads yacht charter (in particular) image to s3 - maybe modify this to handle all uploads for admin side
  Route.post('upload/:bucket?', 'Admin/ContentController.upload')
  Route.get('countries', 'Admin/ContentController.countries') // gets the list of countries to update popular
  Route.post('popularcountry', 'DestinationsController.updatePopularCountry') // sets the "popular" field on countries
  Route.get('destinations', 'Admin/ContentController.destinations')

  Route.get('list/:type', 'Admin/ContentController.index')
  Route.post('store', 'Admin/ContentController.store').validator('StoreContent')
  Route.post('update/:id', 'Admin/ContentController.update').validator('StoreContent')
  Route.delete('delete/:id', 'Admin/ContentController.delete')
}).prefix('api/v1/sc-secret-admin/content').middleware('auth').middleware('adminAuth')

/*
  Reviews
*/
Route.group(() => {
  Route.get('/', 'Admin/ReviewController.index')
  Route.post('/update', 'Admin/ReviewController.update')
}).prefix('api/v1/sc-secret-admin/reviews').middleware('auth').middleware('adminAuth')

/*
  Contacts
*/
Route.group(() => {
  Route.get('/', 'Admin/ContactController.index')
  Route.post('/delete/:id', 'Admin/ContactController.delete')
}).prefix('api/v1/sc-secret-admin/contacts').middleware('auth').middleware('adminAuth')

Route.group(() => {
  Route.get('/', 'Admin/BookingController.index')
  Route.post('/delete/:id', 'Admin/BookingController.delete')
}).prefix('api/v1/sc-secret-admin/bookings').middleware('auth').middleware('adminAuth')

Route.group(() => {
  Route.get('groups', 'Admin/LinksController.index')
  Route.get('links/:group_id', 'Admin/LinksController.pageLinkIndex')
  Route.get('pagetitles', 'Admin/LinksController.pageTitles')

  Route.post('storegroup', 'Admin/LinksController.storeGroup')
  Route.post('updategroup/:group_id', 'Admin/LinksController.updateGroup')
  Route.post('storelink/:group_id', 'Admin/LinksController.storeLink')
  Route.post('orderlinks/:group_id', 'Admin/LinksController.orderLinks')

  Route.delete('deletegroup/:group_id', 'Admin/LinksController.deleteGroup')
  Route.delete('deletelink/:group_id/:page_id', 'Admin/LinksController.deleteLink')
}).prefix('api/v1/sc-secret-admin/links').middleware('auth').middleware('adminAuth')

/*
  NuxtJS routes
*/
Route.any('/sc-secret-admin-cms', 'NuxtController.render')

// admin
Route.group(() => {
  Route.any('*', 'NuxtController.render')
}).prefix('/sc-secret-admin-cms')
