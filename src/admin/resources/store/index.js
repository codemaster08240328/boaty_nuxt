import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'

function restoreState (store) {
  if (process.browser && store) {
    if (window.__NUXT__ && window.__NUXT__.state) {
      store.replaceState(window.__NUXT__.state)
      delete window.__NUXT__.state
    }
  }
}

function createPersistedStateWrapper (store) {
  if (process.browser && store) {
    createPersistedState()(store)
  }
}

const createStore = () => {
  return new Vuex.Store({
    plugins: [restoreState, createPersistedStateWrapper],
    state: {
      admin: {
        countries: [],
        areas: [],
        bases: []
      },
      destinations: [],
      searchRegions: [],
      locales: [
        'en-us',
        'en-gb'
      ],
      locale: 'en-us',
      authUser: '',
      query: {
        region: '',
        boattype: '',
        date: ''
      }
    },
    mutations: {
      setDestinations (state, destinations) {
        state.destinations = destinations
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
      setSearchBoats (state, query) {
        state.query.region = query.region
        state.query.boattype = query.boattype
        state.query.date = query.date
      },
      setUser (state, user) {
        state.authUser = user
      }
    },
    actions: {
      async nuxtServerInit ({ commit }, { app, req }) {
        const cookies = (req) ? req.headers.cookie : false
        // app.store.state.defaultCurrency = app.store.state.currencies[app.store.state.locale]
        if (cookies) {
          if (cookies.includes('sc_auth')) {
            try {
              const { data } = await app.$axios.get('api/v1/user/auth-test')
              commit('setUser', data)
            } catch (error) {
              throw new Error(error)
            }
          }
        }
      },
      async login ({ commit }, { email, password }) {
        try {
          const { data } = await this.$axios.post('/api/v1/user/login', { email, password })
          commit('setUser', data)
          return data
        } catch (error) {
          if (error.response && error.response.status === 401) {
            throw new Error('Bad credentials')
          }
          throw error
        }
      },
      async logout ({ commit }) {
        await this.$axios.post('/api/v1/user/logout')
        commit('SET_USER', null)
      },
      async pages ({ commit }, { data }) {
        const result = await this.$axios.post('/api/v1/sc-secret-admin/content/pages', { data })
        return result
      }
    }
  })
}

export default createStore
