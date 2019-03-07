<template>
  <v-container fluid text-xs-center>
    <v-layout row wrap justify-center>
      <v-flex xs12>
        <v-select
          prepend-icon="fa-search"
          v-bind:items="regions"
          item-text="Name"
          item-value="Url"
          :label="$t('simpleSearch.searchRegion')"
          autocomplete
          clearable
          v-model="query.region"
          :dark="lightText"
        >
          <template slot="item" slot-scope="data">
            <template v-if="typeof data.item !== 'object'">
              <v-list-tile-content v-text="data.item"></v-list-tile-content>
            </template>
            <template v-else>
              <v-list-tile-avatar>
                <v-icon>fa-{{data.item.Icon}}</v-icon>
              </v-list-tile-avatar>
              <v-list-tile-content>
                <v-list-tile-title v-html="data.item.Name"></v-list-tile-title>
              </v-list-tile-content>
            </template>
          </template>
        </v-select>
      </v-flex>
      <v-flex xs12 sm6>
        <v-select
          prepend-icon="fa-ship"
          v-bind:items="boattypes"
          item-text="Name"
          item-value="ID"
          :label="$t('simpleSearch.selectBoat')"
          v-model="query.boattype"
          :dark="lightText"
        >
        </v-select>
      </v-flex>
      <v-flex xs12 sm6>
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
            :label="$t('simpleSearch.selectDate')"
            v-model="query.date"
            prepend-icon="event"
            readonly
            :dark="lightText"
          ></v-text-field>
          <v-date-picker
            ref="picker"
            v-model="query.date"
            :min="new Date().toISOString().substr(0, 10)"
            max="2020-01-01"
            :allowed-dates="allowedDates"
            @change="save"
          ></v-date-picker>
        </v-menu>
      </v-flex>
      <v-flex xs12 style="max-width: 200px;">
        <v-btn class="cyan darken-2 white--text" flat large block v-on:click="searchBoats">{{ $t('utility.search') }}</v-btn>
        <v-snackbar
          :timeout="3000"
          color="info"
          absolute
          bottom
          v-model="snackbar"
        >
          {{ $t('simpleSearch.simpleFailure') }}
          <v-btn dark flat @click.native="snackbar = false">Close</v-btn>
        </v-snackbar>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
export default {
  props: [
    'lightText'
  ],
  methods: {
    searchBoats () {
      if (this.query.date == null || this.query.region == null || this.query.boattype == null) {
        this.snackbar = true
      } else {
        this.$store.commit('setSearchBoats', this.query)
        // set cookies for serverside
        this.$cookie.set('querydate', this.query.date, 365)
        this.$cookie.set('queryregion', this.query.region, 365)
        this.$cookie.set('queryboattype', this.query.boattype, 365)

        // this.$ga.event({
        //   eventCategory: 'interaction',
        //   eventAction: 'search',
        //   eventValue: 0
        // })

        const path = this.$i18n.path('/search' + this.query.region)

        if (this.$route.path !== path) {
          this.$router.push({ 'path': path })
        } else {
          this.$router.go()
        }
      }
    },
    allowedDates: val => {
      const d = new Date(val)
      if (d.getDay() === 6) {
        return 1
      } else {
        return 0
      }
    },
    save (date) {
      console.log(date)
      this.$refs.menu.save(date)
    }
  },
  async mounted () {
    if (this.loaded === false) {
      this.loaded = true
      let { data } = await this.$axios.get('/api/v1/search/regions')
      this.regions = data
    }

    if (this.$store.state.search) {
      if (this.$store.state.search.region) {
        this.query.region = this.$store.state.search.region
      }

      if (this.$store.state.search.boattype) {
        this.query.boattype = parseInt(this.$store.state.search.boattype)
      }

      const d = new Date()
      if (this.$store.state.search.date && new Date(this.$store.state.search.date) >= d.setHours(0, 0, 0, 0)) {
        this.query.date = this.$store.state.search.date
      }
    }
  },
  watch: {
    menu (val) {
      val && this.$nextTick(() => (this.$refs.picker.activePicker = 'MONTH'))
    }
  },
  data () {
    return {
      countries: 'default',
      a1: null,
      loaded: false,
      regions: [],
      boattypes: [{
        ID: 3,
        Name: 'Gulet'
      },
      {
        ID: 4,
        Name: 'Sailing Yacht'
      },
      {
        ID: 5,
        Name: 'Catamaran'
      },
      {
        ID: 6,
        Name: 'Motor Yacht'
      }],
      search: null,
      menu: null,
      snackbar: false,
      query: {
        region: null,
        boattype: null,
        date: ''
      },
      states: 'dark'
    }
  }
}
</script>
