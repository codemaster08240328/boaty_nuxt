<template>
  <v-layout @click="fullScreen()" justify-center align-center>
    <v-select
      single-line
      prepend-icon="fa-search"
      :items="items"
      item-text="Name"
      item-value="Url"
      :label="$t('SingleInputSearch.label')"
      :search-input.sync="search"
      :loading="loading"
      autocomplete
      clearable
      :readonly="$vuetify.breakpoint.xsOnly"
      flat
      open-on-clear
      solo
      max-height="300"
      v-model="query.region"
      @input="searchBoats"
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
    <v-btn class="cyan darken-2 white--text hidden-xs-only" flat @click="searchBoats">{{ $t('utility.search') }}</v-btn>

    <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
      <v-card>
        <v-toolbar class="cyan darken-3 white--text">
          <v-btn icon dark @click.native="dialog = false">
            <v-icon>close</v-icon>
          </v-btn>
          <v-toolbar-title>Search</v-toolbar-title>
        </v-toolbar>
        <v-layout>
          <v-select
            class="fullscreenSingleInputSearch"
            single-line
            prepend-icon="fa-search"
            :items="items"
            item-text="Name"
            item-value="Url"
            :label="$t('SingleInputSearch.label')"
            :search-input.sync="search"
            :loading="loading"
            autocomplete
            clearable
            flat
            open-on-clear
            solo
            dense
            max-height="180"
            v-model="query.region"
            @input="searchBoats"
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
        </v-layout>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
import { filter } from 'lodash'

export default {
  methods: {
    fullScreen () {
      if (this.$vuetify.breakpoint.xsOnly) {
        this.dialog = true
        setTimeout(function () {
          const element = document
            .querySelector('.fullscreenSingleInputSearch')
          element.click()
          // })
        }, 500)
      }
    },
    searchBoats () {
      const that = this
      this.$nextTick(function () {
        if (that.query.region) {
          console.log('searching', that.query)
          that.$store.commit('setSearch', that.query)
          that.currentItem = filter(that.regions, (o) => {
            return o.Url === that.$store.state.search.region
          })
          that.items = that.currentItem
          // set cookies for serverside
          that.$interaction.setDataInCookies({ name: 'search', data: that.query })
          const path = that.$i18n.path('/search' + that.query.region)

          const queryparams = that.$interaction.getDataFromCookie({ name: 'search' })
          delete queryparams.country
          delete queryparams.area
          delete queryparams.base
          delete queryparams.version
          delete queryparams.region

          if (that.$route.path !== path) {
            that.$router.push({ path: path, query: queryparams })
            // that.$router.push({ 'path': path, query: queryparams })
          } else {
            that.$router.go()
          }
        }
      })
    },
    querySelections (v) {
      this.loading = true
      // Simulated ajax query
      setTimeout(() => {
        this.items = this.regions.filter(e => {
          return (e.Name || '').toLowerCase().indexOf((v || '').toLowerCase()) > -1
        })
        this.loading = false
      }, 500)
    }
  },
  async mounted () {
    if (this.loaded === false) {
      this.loaded = true
      const { data } = await this.$axios.get('/api/v1/search/regions')
      this.regions = data
    }

    if (this.$store.state.search) {
      if (this.$store.state.search.region) {
        this.query.region = this.$store.state.search.region
        this.currentItem = filter(this.regions, (o) => {
          return o.Url === this.$store.state.search.region
        })
        this.items = this.currentItem
      }
    }
  },
  watch: {
    search (val) {
      val && this.querySelections(val)
    }
  },
  data () {
    return {
      search: null,
      loading: false,
      dialog: false,
      loaded: false,
      regions: [],
      items: [],
      currentItem: [],
      query: {
        region: null
      }
    }
  }
}
</script>
