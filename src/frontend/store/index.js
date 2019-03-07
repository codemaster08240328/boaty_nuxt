import Vuex from 'vuex'
// import createPersistedState from 'vuex-persistedstate'
import { merge, each, map, assign } from 'lodash'

// function restoreState (store) {
//   if (process.browser && store) {
//     if (window.__NUXT__ && window.__NUXT__.state) {
//       store.replaceState(window.__NUXT__.state)
//       // setTimeout(() => {
//       //   delete window.__NUXT__.state
//       // }, 1000)
//       delete window.__NUXT__.state

//       console.log(store)
//     }
//   }
// }

// function createPersistedStateWrapper (store) {
//   if (process.browser && store) {
//     console.log('persisted')
//     console.log(store)
//     createPersistedState()(store)
//   }
// }

const createStore = () => {
  return new Vuex.Store({
    // plugins: [restoreState, createPersistedStateWrapper],
    state: {
      gmaps: [],
      destinations: [],
      popularLocations: [],
      searchRegions: [],
      sailingItineraries: {},
      yachtCharters: {},
      locales: [
        'en-us',
        'en-gb'
      ],
      locale: 'en-us',
      user: {
        fullName: '',
        email: '',
        phone_number: '',
        login_source: '',
        /**
         * meta holds the information related to what a user is doing, be is the number of adults/children, to searches
         */
        meta: {
          searches: [],
          BoatIDsVisited: []
        },
        // used for auth (obviously)
        auth: {
          type: 'Bearer',
          token: false
        }
      },
      search: {},
      query: {},
      contact: {
        name: '',
        email: '',
        phone_number: ''
      },
      fx: {},
      currencies: {
        'en-us': 'USD',
        'en-gb': 'GBP'
      },
      defaultCurrency: 'EUR'
    },
    getters: {
      defaultCurrency: state => {
        return state.defaultCurrency
      },
      queryparams: state => {
        const queryparams = state.search
        // delete queryparams.version
        // delete queryparams.region
        // delete queryparams.country
        // delete queryparams.area
        // delete queryparams.base
        // delete queryparams.Region
        // delete queryparams.Country
        // delete queryparams.Area
        // delete queryparams.Base
        // delete queryparams.altered
        // if (queryparams.date === null) {
        //   delete queryparams.date
        // }
        return queryparams
      }
    },
    mutations: {
      setDestinations (state, destinations) {
        state.destinations = destinations
      },
      setPopularLocations (state, popularLocations) {
        state.popularLocations = popularLocations
      },
      setGMAPS (state, gmaps) {
        state.gmaps = gmaps
      },
      setCountries (state, countries) {
        state.admin.countries = countries
      },
      setAreas (state, areas) {
        state.admin.areas = areas
      },
      setBases (state, bases) {
        state.admin.bases = bases
      },
      setLang (state, locale) {
        if (state.locales.indexOf(locale) !== -1) {
          state.locale = locale
        }
      },
      setSearch (state, search) {
        for (const key of Object.keys(search)) {
          state.search[key] = search[key]
        }
      },
      resetSearch (state, version) {
        state.search = {
          version: version
        }
      },
      setUser (state, user) {
        state.user = merge(state.user, user)
      },
      setFX (state, fx) {
        state.fx = fx
      },
      setSailingItineraries (state, itineraries) {
        state.sailingItineraries = itineraries
      },
      setYachtCharters (state, charters) {
        state.yachtCharters = charters
      },
      setDefaultCurrency (state, currency) {
        state.defaultCurrency = currency
      },
      setContactUser (state, user) {
        state.user = merge(state.user, user)
      }
    },
    actions: {
      async nuxtServerInit ({ commit }, { res, req, app, route }) {
        console.log('nuxt server init')
        const cookies = (req) ? req.headers.cookie : false

        console.log('async route', route.query)
        let cookieset = false

        /*
          TODO: what a mess
        */
        if (
          route.query.prices ||
          route.query.lengths ||
          route.query.years ||
          route.query.boatType ||
          route.query.cabins ||
          route.query.toilets ||
          route.query.sortby ||
          route.query.date) {
          let queryparams = route.query

          let holder = {
            prices: [],
            lengths: [],
            years: []
          }

          if (Array.isArray(queryparams.prices)) {
            each(queryparams.prices, (p) => {
              holder.prices.push(p.split(','))
            })
          } else if (queryparams.prices) {
            holder.prices.push(queryparams.prices.split(','))
          }

          if (Array.isArray(queryparams.lengths)) {
            each(queryparams.lengths, (p) => {
              holder.lengths.push(p.split(','))
            })
          } else if (queryparams.lengths) {
            holder.lengths.push(queryparams.lengths.split(','))
          }

          if (Array.isArray(queryparams.years)) {
            each(queryparams.years, (p) => {
              holder.years.push(p.split(','))
            })
          } else if (queryparams.years) {
            holder.years.push(queryparams.years.split(','))
          }

          queryparams.prices = holder.prices
          queryparams.lengths = holder.lengths
          queryparams.years = holder.years

          queryparams.boatType = map(queryparams.boatType, (bt) => {
            if (bt && bt != null && !isNaN(bt)) {
              return parseInt(bt)
            }
          })
          queryparams.cabins = map(queryparams.cabins, (bt) => {
            if (bt) {
              return parseInt(bt)
            }
          })
          queryparams.toilets = map(queryparams.toilets, (bt) => {
            if (bt) {
              return parseInt(bt)
            }
          })
          queryparams.cabins = map(queryparams.cabins, (bt) => {
            if (bt) {
              return parseInt(bt)
            }
          })
          queryparams.sortby = parseInt(queryparams.sortby)
          if (!route.query.date) {
            delete queryparams.date
          }
          queryparams.version = 4

          const search = app.$interaction.getDataFromCookie({ name: 'search', json: true })
          res.setHeader('Set-Cookie', [`search=${JSON.stringify(assign(search, queryparams))};Path=/`])
          console.log('before', assign(search, queryparams))
          commit('setSearch', assign(search, queryparams))
          cookieset = true
        }

        if (cookies && app.$axios) {
          const search = app.$interaction.getDataFromCookie({ name: 'search', json: true })
          if (search.version !== 4 && !cookieset) {
            let reset = { version: 4 }
            if (search) {
              res.setHeader('Set-Cookie', [`search=${JSON.stringify(assign(search, reset))};Path=/`])
            } else {
              res.setHeader('Set-Cookie', [`search=${JSON.stringify(reset)};Path=/`])
            }
            commit('resetSearch', reset)
          } else if (search.version === 4 && !cookieset) {
            commit('setSearch', search)
          }
        }

        // get rates
        try {
          const { data } = await app.$axios.get('/api/v1/menu')
          commit('setFX', data.fxRates)
          commit('setSailingItineraries', data.sailingItineraries)
          commit('setYachtCharters', data.yachtCharters)
        } catch (e) {
          throw new Error(e)
        }
      }
    }
  })
}

export default createStore
