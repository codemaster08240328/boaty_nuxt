<template>
  <section>
    <!-- header -->
    <v-parallax 
    class="yacht-charter" 
    v-if="data.page && data.page.pageImage.length > 0" 
      :src="s3 + `uploads/${data.page.type}/` + data.page.pageImage[0].fileName" height="420">
      <v-layout
        column
        align-center
        justify-center
        class="white--text text-xs-center"
      >
        <h1 v-if="data.page.title" class="white--text display-3">{{ data.page.title }}</h1>
        <div v-if="data.page.subtitle" class="subheading mb-3 display-1 text-xs-center">{{ data.page.subtitle }}</div>
        <enquire :buttonTitle="'Enquire Now & Set Sail'" :buttonClass="'cyan darken-3 ma-2'"></enquire>
      </v-layout>
      <v-layout justify-center align-center class="parallax-psp hidden-sm-and-down" row wrap>
        <v-flex xs2 offset-xs1>
          <div class="mb-1">
            <h4 class="body-1">Our global partners</h4>
          </div>
          <div class="items">
            <div style="width: 0px; height: 100px;"></div>
            <img class="mx-1" width="100" src="https://s3.eu-west-2.amazonaws.com/sc30/static/payment/pantaenius.png"/>
          </div>
        </v-flex>
        <v-flex xs6>
          <div class="mb-1">
            <h4 class="body-1">SailChecker in the press</h4>
          </div>
          <div class="items">
            <img 
              style="max-width: 600px;" 
              src="https://s3.eu-west-2.amazonaws.com/sc30/static/awards/in-the-press-min.png" 
              alt="SailChecker's appearances in press"
            />
          </div>
        </v-flex>
      </v-layout>
    </v-parallax>


    <v-container grid-list-xs fluid class="psp text-xs-center pb-3 mb-3 cyan darken-3 white--text">
      <v-container grid-list-xs>
        <v-layout row wrap>
          <v-flex class="py-1" xs12 sm4>
            <i class="material-icons">location_searching</i><h3 class="headline">All Sailing Destinations</h3>
          </v-flex>

          <v-flex class="py-1" xs12 sm4>
            <i class="material-icons">security</i><h3 class="headline">Your Questions Answered</h3>
          </v-flex>

          <v-flex class="py-1" xs12 sm4>
            <i class="material-icons">credit_card</i><h3 class="headline">Book With Confidence</h3>
          </v-flex>
        </v-layout>
      </v-container>
    </v-container>

    <v-breadcrumbs id="breadcrumbs" v-if="!isMobile" justify-center>
      <v-icon slot="divider">forward</v-icon>
      <v-breadcrumbs-item :href="breadcrumb['@id']" v-for="breadcrumb in breadcrumbs" :key="breadcrumb['@id']">
        {{ breadcrumb.name }}
      </v-breadcrumbs-item>
    </v-breadcrumbs>

    <v-container v-if="data.page">
      <v-layout row wrap>
        <v-flex class="main-content" v-if="data.page.body" xs12>
          <div v-html="data.page.body"></div>
        </v-flex>
      </v-layout>
    </v-container>

    <v-container grid-list-xs class="mb-4" v-if="data.page.template === 'search'">
      <v-layout row wrap>
        <v-flex xs12 class="cyan darken-3 pa-1">
          <async-single-input-search
            v-on:updateBoats="updateBoats"
            v-on:boatsLoading="loadingBoats"
            v-on:updateParams="updateParams"
          ></async-single-input-search>
        </v-flex>
        <v-flex xs12 v-if="isMobile">
          <v-btn flat class="cyan darken-3 white--text pull-right" @click="mobileFilters">
            Filter
          </v-btn>
        </v-flex>
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
              <v-card dense class="mt-4">
                <v-card-actions>
                  <v-list three-line dense>
                    <v-list-tile avatar>
                      <v-list-tile-action>
                        <v-icon color="green" class="display-1">money_off</v-icon>
                      </v-list-tile-action>
                      <v-list-tile-content>
                        <v-list-tile-title class="subheading">{{ $t('searchPage.moneyofftitle') }}</v-list-tile-title>
                        <div>{{ $t('searchPage.moneyoff') }}</div>
                      </v-list-tile-content>
                    </v-list-tile>

                    <v-list-tile avatar>
                      <v-list-tile-action>
                        <v-icon color="blue" class="display-1">credit_card</v-icon>
                      </v-list-tile-action>
                      <v-list-tile-content>
                        <v-list-tile-title class="subheading">{{ $t('searchPage.securetitle') }}</v-list-tile-title>
                        <div>{{ $t('searchPage.secure') }}</div>
                      </v-list-tile-content>
                    </v-list-tile>

                    <v-list-tile avatar>
                      <v-list-tile-action>
                        <v-icon color="orange" class="display-1">event_available</v-icon>
                      </v-list-tile-action>
                      <v-list-tile-content>
                        <v-list-tile-title class="subheading">{{ $t('searchPage.availabletitle') }}</v-list-tile-title>
                        <div>{{ $t('searchPage.available') }}</div>
                      </v-list-tile-content>
                    </v-list-tile>
                  </v-list>
                </v-card-actions>
              </v-card>
            </v-flex>
          </v-layout>
        </v-flex>
        <v-flex xs12 sm8 md9 class="boats-container">
          <div v-show="loading" class="overlay-loader">
            <v-progress-circular indeterminate :size="120" :width="10" color="orange"></v-progress-circular>
          </div>
          <v-layout row wrap>
            <v-flex xs12>
              <data-table-boats :boats="boats"></data-table-boats>
            </v-flex>
          </v-layout>
        </v-flex>
      </v-layout>
    </v-container>

    <!-- itineraries -->
    <pages-card 
      v-if="data.pages.sailingItineraries" 
      :pages="data.pages.sailingItineraries"
      :title="$t('blogCard.itineraries', { location: pageType })"
      :s3Url="`${s3}uploads/sailing-itineraries/`"
    ></pages-card>

    <full-width-page-title :title="'Top Yacht Charter Destinations'"></full-width-page-title>
    <popular-locations class="mb-2" :locations="data.destinations"/>
  </section>
</template>

<style scoped>
.why-us p {
  margin: 0 auto;
  width: 80%
}
h3 {
  display: inline;
}
.psp i {
  position: relative;
  top: 9px;
  right: 4px;
  font-size: 36px;
}
.parallax-psp {
  justify-content: center;
}
.parallax-psp .items {
  display: flex;
  align-items: center;
  justify-content: center flex-end;
}
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
import PagesCard from '~/components/PagesCard'
import SingleInputSearch from '~/components/SingleInputSearch'
import DataTableBoats from '~/components/DataTableBoats'
import AdvancedSearch from '~/components/AdvancedSearch'
import SearchFilters from '~/assets/js/searchFilters'
import PopularLocations from '~/components/PopularLocations'
import FullWidthPageTitle from '~/components/FullWidthPageTitle'
import AsyncSingleInputSearch from '~/components/AsyncSingleInputSearch'
import MetaService from '~/services/meta'
import Enquire from '~/components/Enquire'
import { assign } from 'lodash'
// import MetaService from '~/services/meta'

export default {
  transition: {
    name: 'page',
    mode: 'out-in'
  },
  components: {
    PagesCard,
    AdvancedSearch,
    DataTableBoats,
    SingleInputSearch,
    PopularLocations,
    FullWidthPageTitle,
    AsyncSingleInputSearch,
    Enquire
  },
  methods: {
    updateBoats (val) {
      console.log('update boat')
      this.boats = val.boats
      this.paginate = val.paginate
    },
    updateParams (val) {
      this.params = val
    },
    loadingBoats (val) {
      console.log('loading', val)
      this.loading = val
    },
    mobileFilters () {
      this.dialog = !this.dialog
    }
  },
  async asyncData ({ params, app, breadcrumbs, isMobile, error, route, store }) {
    try {
      console.log('route', route.path)
      const routeParts = route.path.split('/')
      let originalPageType = ''
      let pageType = ''

      if ((originalPageType = routeParts[routeParts.length - 1]) === '') {
        originalPageType = routeParts[routeParts.length - 2]
      }

      const plural = [
        'blog'
      ]

      if (originalPageType.includes(plural)) {
        pageType = `${originalPageType}s`
      } else {
        pageType = originalPageType
      }

      let { data } = await app.$axios.get(`/api/v1/pages/products${route.path}`)

      const filters = SearchFilters
      const cookieFilters = assign(store.state.search, data.search)
      cookieFilters.lengths = SearchFilters.valueToId(filters.lengths, cookieFilters.lengths)
      cookieFilters.years = SearchFilters.valueToId(filters.years, cookieFilters.years)
      cookieFilters.prices = SearchFilters.valueToId(filters.prices.options, cookieFilters.prices)

      return {
        data: data,
        pageType: pageType.charAt(0).toUpperCase() + pageType.slice(1).replace(/-/g, ' '), // used for plurals
        s3: process.env.s3.url,
        s3Content: process.env.s3.url + 'uploads' + route.path,
        breadcrumbs: breadcrumbs,
        params: cookieFilters,
        filters,
        isMobile: isMobile,
        dialog: false,
        boats: data.boats,
        paginate: {
          total: (data.boats_paginate) ? data.boats_paginate.total : false,
          currentPage: 1,
          perPage: 18
        },
        loading: false,
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
      error({ statusCode: 404, message: 'Page not found' })
    }
  },
  head () {
    const breadcrumbsJSON = MetaService.breadcrumbsJSON(this.breadcrumbs)
    const data = this.data
    let s3Content = this.s3Content
    console.log()
    if (s3Content.substring(s3Content.length - 1) === '/') {
      s3Content = s3Content.substring(0, s3Content.length - 1)
    }

    const meta = MetaService.base({
      title: data.page.title,
      description: data.page.description,
      image: (data.page.pageImage) ? s3Content + '/' + data.page.pageImage[0].fileName : false,
      creator: (data.page.user) ? data.page.user.fullName : 'Chris Lait',
      type: 'article',
      url: this.$i18n.path(this.$route.path)
    })

    return {
      title: data.page.title,
      meta: meta,
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
      ],
      script: [
        { innerHTML: breadcrumbsJSON, type: 'application/ld+json' }
      ],
      __dangerouslyDisableSanitizers: ['script']
    }
  }
}
</script>

