<template>
  <section>
    <v-card class="cyan darken-3" dark>
      <v-card-actions>
        <v-container grid-list-xs>
          <v-layout row wrap>
            <v-flex xs12>
              <v-menu
                ref="menu"
                :close-on-content-click="false"
                v-model="menu"
                full-width
                max-width="290px"
                min-width="290px"
              >
                <v-text-field
                  slot="activator"
                  :label="$t('searchPage.selectDate')"
                  :hint="$t('searchPage.selectDateHint')"
                  persistent-hint
                  v-model="selected.date"
                  prepend-icon="event"
                  readonly
                  solo
                  light
                ></v-text-field>
                <v-date-picker
                  ref="picker"
                  v-model="selected.date"
                  :min="new Date().toISOString().substr(0, 10)"
                  max="2021-01-01"
                  @change="save"
                ></v-date-picker>
                <!-- TODO: add this in for certain countries :allowed-dates="allowedDates" -->
              </v-menu>
            </v-flex>
            <v-flex xs12>
              <v-select
                dense
                multiple
                chips
                color="blue"
                deletable-chips
                :label="$t('searchPage.selectBoat')"
                :hint="$t('searchPage.selectBoatHint')"
                persistent-hint
                item-text="Name"
                item-value="ID"
                :items="filters.boatType"
                v-model="selected.boatType"
              >
              </v-select>
            </v-flex>
            <v-flex xs12>
              <v-select
                dense
                multiple
                chips
                color="blue"
                deletable-chips
                :label="$t('searchPage.totalPrice')"
                :items="filters.prices.options"
                v-model="selected.prices"
                :hint="$t('searchPage.priceHint', { currency: defaultCurrency})"
                persistent-hint
              ></v-select>
            </v-flex>
            <v-flex xs12>
              <v-select
                dense
                multiple
                chips
                color="blue"
                deletable-chips
                :label="$t('searchPage.totalLength')"
                :items="filters.lengths"
                v-model="selected.lengths"
                :hint="$t('searchPage.lengthHint')"
                persistent-hint
              ></v-select>
            </v-flex>
            <v-flex xs12>
              <v-select
                dense
                multiple
                chips
                color="blue"
                deletable-chips
                :label="$t('searchPage.cabins')"
                :items="filters.cabins"
                v-model="selected.cabins"
                :hint="$t('searchPage.cabinsHint')"
                persistent-hint
              ></v-select>
            </v-flex>
            <v-flex xs12>
              <v-select
                dense
                multiple
                chips
                color="blue"
                deletable-chips
                :label="$t('searchPage.years')"
                :items="filters.years"
                v-model="selected.years"
                :hint="$t('searchPage.yearsHint')"
                persistent-hint
              ></v-select>
            </v-flex>
            <v-flex xs12>
              <v-select
                dense
                multiple
                chips
                color="blue"
                deletable-chips
                :label="$t('searchPage.toilets')"
                :items="filters.toilets"
                v-model="selected.toilets"
                :hint="$t('searchPage.toiletsHint')"
                persistent-hint
              ></v-select>
            </v-flex>
          </v-layout>
        </v-container>
      </v-card-actions>
    </v-card>
  </section>
</template>

<style scoped>
</style>

<script>
import { debounce } from 'lodash'
import Money from 'money'
import InteractionService from '~/services/Interaction'
// import querystring from 'querystring'

export default {
  props: [
    'selected',
    'filters',
    'params'
  ],
  methods: {
    allowedDates: val => {
      const d = new Date(val)
      if (d.getDay() === 6) {
        return 1
      } else {
        return 0
      }
    },
    save (date) {
      this.$refs.menu.save(date)
    },
    updateFilters: debounce(function () { // occurs on change of a filter
      console.log('updating filters')
      this.$emit('boatsLoading', true)
      const post = InteractionService.setAdvancedSearch(this.selected, this.params, this.filters)

      this.$store.commit('setSearch', post)
      this.$interaction.setDataInCookies({ name: 'search', data: post })

      // slow the api call, as too "quick"
      setTimeout(async () => {
        try {
          const { data } = await this.$axios.post('/api/v1/search/boats/filter', post)
          const queryparams = post
          delete queryparams.country
          delete queryparams.area
          delete queryparams.base

          if (!post.date) {
            delete queryparams.date
          }

          if (post.boatType.length === 0) {
            delete queryparams.boatType
          }

          this.$router.push({ path: this.$route.path, query: queryparams })

          this.$emit('updateBoats', {
            boats: data.boats,
            paginate: {
              total: (data.boats_paginate) ? data.boats_paginate.total : false,
              currentPage: 1
            }
          })

          this.$emit('boatsLoading', false)
        } catch (e) {
          console.log(e)
        }
      }, 500)
    }, 500)
  },
  watch: {
    selected: {
      handler: function (val, oldVal) {
        this.updateFilters() // call it in the context of your component object
      },
      deep: true
    }
  },
  computed: {
    defaultCurrency () {
      Money.rates = this.$store.state.fx.rates
      Money.base = this.$store.state.fx.base

      const currencySymbols = {
        'USD': '$', // US Dollar
        'EUR': '€', // Euro
        'CRC': '₡', // Costa Rican Colón
        'GBP': '£', // British Pound Sterling
        'ILS': '₪', // Israeli New Sheqel
        'INR': '₹', // Indian Rupee
        'JPY': '¥', // Japanese Yen
        'KRW': '₩', // South Korean Won
        'NGN': '₦', // Nigerian Naira
        'PHP': '₱', // Philippine Peso
        'PLN': 'zł', // Polish Zloty
        'PYG': '₲', // Paraguayan Guarani
        'THB': '฿', // Thai Baht
        'UAH': '₴', // Ukrainian Hryvnia
        'VND': '₫' // Vietnamese Dong
      }

      if (currencySymbols[this.$store.getters.defaultCurrency] !== undefined) {
        this.symbol = currencySymbols[this.$store.getters.defaultCurrency]
      } else {
        this.symbol = false
      }

      for (let index of Object.keys(this.filters.prices.options)) {
        const option = this.filters.prices.options[index]

        if (option.value === 7) {
          this.filters.prices.options[index].current[0] = Math.ceil(Money.convert(this.filters.prices.options[index].base[0],
            {
              from: this.filters.prices.originalCurrency,
              to: this.$store.getters.defaultCurrency
            }))

          this.filters.prices.options[index].text = this.symbol + this.filters.prices.options[index].current[0] + '+'
        } else {
          this.filters.prices.options[index].current[0] = Math.ceil(Money.convert(this.filters.prices.options[index].base[0],
            {
              from: this.filters.prices.originalCurrency,
              to: this.$store.getters.defaultCurrency
            }))

          this.filters.prices.options[index].current[1] = Math.ceil(Money.convert(this.filters.prices.options[index].base[1],
            {
              from: this.filters.prices.originalCurrency,
              to: this.$store.getters.defaultCurrency
            }))

          this.filters.prices.options[index].text = this.symbol + this.filters.prices.options[index].current[0] + ' - ' + this.symbol + this.filters.prices.options[index].current[1]
        }
      }

      this.filters.prices.currency = this.$store.getters.defaultCurrency
    }
  },
  data () {
    return {
      menu: false
    }
  }
}
</script>
