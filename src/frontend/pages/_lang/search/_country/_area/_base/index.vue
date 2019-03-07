<template>
  <section class="searchPage">
    <v-breadcrumbs id="breadcrumbs" v-if="!isMobile" justify-center>
      <v-icon slot="divider">forward</v-icon>
      <v-breadcrumbs-item :href="breadcrumb['@id']" v-for="breadcrumb in breadcrumbs" :key="breadcrumb['@id']">
        {{ breadcrumb.name }}
      </v-breadcrumbs-item>
    </v-breadcrumbs>

    <v-container class="cyan darken-3 pa-1" grid-list-xs>
      <v-layout row wrap>
        <v-flex xs12>
          <single-input-search></single-input-search>
        </v-flex>
      </v-layout>
    </v-container>

    <v-container grid-list-xs>
      <v-layout row wrap>
        <v-flex xs12 sm4 md3>
          <v-layout row wrap>
            <v-flex class="filters" v-show="!isMobile" xs12>
              <advanced-search 
                :params="params" 
                :filters="filters" 
                :selected="selected" 
                v-on:updateBoats="updateBoats"
                v-on:boatsLoading="loadingBoats"></advanced-search>
            </v-flex>

            <v-dialog v-if="isMobile" v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
              <v-card class="cyan darken-3">
                <v-toolbar class="cyan darken-4 white--text elevation-20">
                  <v-btn icon dark @click.native="dialog = false">
                    <v-icon>close</v-icon>
                  </v-btn>
                  <v-toolbar-title>Filters</v-toolbar-title>
                </v-toolbar>
                <advanced-search 
                  :params="params" 
                  :filters="filters" 
                  :selected="selected" 
                  v-on:updateBoats="updateBoats"
                  v-on:boatsLoading="loadingBoats">
                </advanced-search>
              </v-card>
            </v-dialog>

            <v-flex xs12 v-if="!isMobile">
              <search-cta :title="title" :s3="s3" :page="page"></search-cta>
            </v-flex>
          </v-layout>
        </v-flex>
        <v-flex xs12 sm8 md9 class="boats-container">
          <div v-show="loading" class="overlay-loader">
            <v-progress-circular indeterminate :size="120" :width="10" color="orange"></v-progress-circular>
          </div>
          <v-layout class="pl-3" row wrap>
            <v-flex xs12 sm12>
              <h1 class="headline pb-1">
                {{  title }}
              </h1>
              <div class="title">
                <p v-if="paginate.total" class="pull-left">{{ $t('searchPage.found', { total: paginate.total }) }}</p>
                <v-btn v-if="isMobile" flat class="cyan darken-3 white--text pull-right" @click="mobileFilters">
                  Filter
                </v-btn>
              </div>
            </v-flex>
            <!-- <v-flex xs12 sm3 class="hidden-xs-only">
              <v-select
                v-bind:items="sortby"
                item-text="text"
                item-value="id"
                v-model="selected.sortby"
                label="Select"
                single-line
                auto
                append-icon="filter_list"
                hide-details
              ></v-select>
            </v-flex> -->
          </v-layout>
          <v-layout row wrap>
            <v-flex xs12>
              <data-table-boats :boats="boats"></data-table-boats>
            </v-flex>
          </v-layout>
        </v-flex>
      </v-layout>
      <v-layout row wrap v-if="isMobile">
        <v-flex xs12>
          <search-cta :title="title" :s3="s3" :page="page"></search-cta>
        </v-flex>
      </v-layout>
    </v-container>

    <pages-card 
      v-if="pages.sailingItineraries" 
      :pages="pages.sailingItineraries"
      :title="$t('blogCard.itineraries', { location: location.data.country_name_en })"
      :s3Url="`${s3}uploads/sailing-itineraries/`"
    ></pages-card>

    <blog-card :wp="wp" :location="params.country"></blog-card>
  </section>
</template>

<style>
  .boats-container {
    position: relative;
  }
  .overlay-loader {
    position: absolute;
    top: 0px;
    bottom: 0px;
    left: 0px;
    z-index: 1;
    right: 0px;
    background: rgba(255,255,255,0.5);
  }
  .overlay-loader .progress-circular {
    position: absolute;
    padding: 2em;
    left: 50%;
    top: 200px;
    transform: translate(-50%, -50%);
  }
  table.table tbody td:first-child, table.table tbody td:not(:first-child), table.table tbody th:first-child, table.table tbody th:not(:first-child), table.table thead td:first-child, table.table thead td:not(:first-child), table.table thead th:first-child, table.table thead th:not(:first-child) {
    padding: 0 12px !important;
  }
</style>

<script>
import SingleInputSearch from '~/components/SingleInputSearch'
import AdvancedSearch from '~/components/AdvancedSearch'
import SearchCta from '~/components/SearchCta'
import MultiBoatCard from '~/components/MultiBoatCard'
import DataTableBoats from '~/components/DataTableBoats'
import BlogCard from '~/components/BlogCard'
import PagesCard from '~/components/PagesCard'
import MetaService from '~/services/meta'
import SearchFilters from '~/assets/js/searchFilters'
export default {
  transition: {
    name: 'page',
    mode: 'out-in'
  },
  components: {
    SingleInputSearch,
    AdvancedSearch,
    SearchCta,
    MultiBoatCard,
    BlogCard,
    DataTableBoats,
    PagesCard
  },
  methods: {
    updateBoats (val) {
      console.log('update boat')
      this.boats = val.boats
      this.paginate = val.paginate
    },
    loadingBoats (val) {
      console.log('loading', val)
      this.loading = val
    },
    mobileFilters () {
      this.dialog = !this.dialog
    }
  },
  async asyncData ({ route, params, store, isMobile, app, breadcrumbs, error }) {
    try {
      const { data } = await app.$axios.get('/api/v1/pages/search/', { params: params })
      const filters = SearchFilters

      const cookieFilters = {...JSON.parse(JSON.stringify(store.state.search)), ...data.search} // (JSON.parse(JSON.stringify(store.state.search))) || data.search
      cookieFilters.lengths = SearchFilters.valueToId(filters.lengths, cookieFilters.lengths)
      cookieFilters.years = SearchFilters.valueToId(filters.years, cookieFilters.years)
      cookieFilters.prices = SearchFilters.valueToId(filters.prices.options, cookieFilters.prices)

      return {
        search: {},
        title: data.title,
        boats: data.boats,
        paginate: {
          total: (data.boats_paginate) ? data.boats_paginate.total : false,
          currentPage: 1,
          perPage: 18
        },
        page: data.page || false,
        wp: data.wp,
        breadcrumbs: breadcrumbs,
        params: params,
        isMobile: isMobile,
        baseUrl: process.env.BASE_URL,
        pages: data.pages || false,
        location: data.location || false,
        s3: process.env.s3.url,
        selected: Object.assign({
          prices: [],
          lengths: [],
          cabins: [],
          years: [],
          toilets: [],
          sortby: 5
        }, cookieFilters)
      }
    } catch (e) {
      // console.log('error', e)
      console.log(e.response.data)
      error({ statusCode: 404, message: 'Page not found' })
    }
  },
  data () {
    // set the data for simple search component
    if (this.$route.params.hasOwnProperty('base')) {
      this.$store.state.search.region = '/' + this.$route.params.country + '/' + this.$route.params.area + '/' + this.$route.params.base + '/'
    } else if (this.$route.params.hasOwnProperty('area')) {
      this.$store.state.search.region = '/' + this.$route.params.country + '/' + this.$route.params.area + '/'
    } else if (this.$route.params.hasOwnProperty('country')) {
      this.$store.state.search.region = '/' + this.$route.params.country + '/'
    }
    const filters = SearchFilters

    return {
      breadcrumbs: [],
      paginate: {
        total: false,
        currentPage: 1,
        perPage: 9
      },
      menu: null,
      wp: false,
      loading: false,
      Menu: true,
      isMobile: false,
      showMenu: false,
      dialog: false,
      page: {
        description: '',
        pageImage: []
      },
      boats: [],
      title: 'Yacht Charter Search',
      filters,
      sortby: [
        {
          text: 'Price (Low To High)',
          id: 1
        },
        {
          text: 'Price (High To Low)',
          id: 2
        },
        {
          text: 'Year Built',
          id: 3
        },
        {
          text: 'Cabins',
          id: 4
        },
        {
          text: 'Recommended',
          id: 5
        }
      ],
      s3: process.env.s3.url
    }
  },
  head () {
    const breadcrumbsJSON = MetaService.breadcrumbsJSON(this.breadcrumbs)
    const searchResultsJSON = MetaService.searchResultsJSON(
      this.boats,
      this.title,
      `${this.baseUrl.slice(0, -1)}${this.$route.path}`
    )

    const meta = MetaService.base({
      title: this.title,
      description: 'SailChecker will help you find the perfect ' + this.title + ' contact us for exclusive deals',
      image: 'https://s3.eu-west-2.amazonaws.com/sc30/static/sailchecker_marina_sunset.jpg',
      creator: 'Chris Lait',
      type: 'website',
      url: this.$i18n.path(this.$i18n.hreflang(this.$route.fullPath))
    })

    return {
      title: this.title,
      meta: meta,
      script: [
        { innerHTML: breadcrumbsJSON, type: 'application/ld+json' },
        { innerHTML: searchResultsJSON, type: 'application/ld+json' }
      ],
      __dangerouslyDisableSanitizers: ['script'],
      link: [
        {
          hid: 'en',
          rel: 'alternate',
          href: this.baseUrl + this.$i18n.hreflang(this.$route.path),
          hreflang: 'x-default'
        }
        // {
        //   hid: 'engb',
        //   rel: 'alternate',
        //   href: this.baseUrl + `gb/${this.$i18n.hreflang(this.$route.path)}`,
        //   hreflang: 'en-gb'
        // }
      ]
    }
  }
}
</script>
