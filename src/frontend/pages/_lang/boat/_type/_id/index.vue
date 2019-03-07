<template>
  <section class="boat-page">
    <v-breadcrumbs id="breadcrumbs" v-if="!isMobile" justify-center>
      <v-icon slot="divider">forward</v-icon>
      <v-breadcrumbs-item :href="breadcrumb['@id']" v-for="breadcrumb in breadcrumbs" :key="breadcrumb['@id']">
        {{ breadcrumb.name }}
      </v-breadcrumbs-item>
    </v-breadcrumbs>
    
    <v-container grid-list-md id="boat-info" class="boat-info">
      <v-layout row wrap>
        <v-flex xs12 md8>
          <v-layout v-if="page.boat.images.length > 0" row wrap class="py-2">
            <v-flex xs12>
              <v-carousel>
                <v-carousel-item
                  v-for="(item,i) in page.boat.images"
                  v-bind:key="i"
                  v-bind:src="'https://s3.eu-west-2.amazonaws.com/sc30/uploads/boats/' + item.url"
                  :transition="'fade'"
                  :reverseTransition="'fade'"
                  height="250"
                ></v-carousel-item>
              </v-carousel>
            </v-flex>
          </v-layout>

          <v-divider></v-divider>

          <v-layout row wrap class="py-2">
            <v-flex xs12>
              <v-card>
                <v-card-title class="cyan darken-3 white--text">
                  <h2 class="title mb-0">Overview</h2>
                </v-card-title>
                <v-card-text>
                  <p class="pa-1 body-2">
                    {{ $t('boatPage.description', { 
                      buildyear: page.boat.BuildYear, 
                      modelname: page.boat.ModelName,
                      cabins: page.boat.Cabins,
                      location: page.boat.location.base_name,
                      country: page.boat.location.country_name,
                      berths: page.boat.Berths
                    }) }}
                  </p>
                </v-card-text>
                <v-card-media v-if="page.boat.layout_image" :src="'https://s3.eu-west-2.amazonaws.com/sc30/uploads/boats/' + page.boat.layout_image" height="300px">
                </v-card-media>
                <v-card-text>
                <v-layout row wrap class="body-2">
                  <v-flex xs12 md4>
                    <p><v-icon>build</v-icon> {{ $t('boatPage.year')}}: {{ page.boat.BuildYear }}</p>
                    <p><v-icon>directions_boat</v-icon> {{ $t('boatPage.length') }}: {{ page.boat.OverallLength }}m</p>
                  </v-flex>
                  <v-flex xs12 md4>
                    <p><v-icon>people</v-icon> {{ $t('boatPage.berths') }}: {{ page.boat.Berths }}</p>
                    <p><v-icon>vpn_key</v-icon> {{ $t('boatPage.cabins') }}: {{ page.boat.Cabins }}</p>
                  </v-flex>
                  <v-flex xs12 md4>
                    <p><v-icon>wc</v-icon> {{ $t('boatPage.showers') }}: {{ page.boat.Toilets }}</p>
                    <p><v-icon>merge_type</v-icon> {{ $t('boatPage.brand') }}: {{ page.boat.BrandName }}, {{ page.boat.TypeName }}</p>
                  </v-flex>
                </v-layout>
                </v-card-text>
              </v-card>
            </v-flex>
          </v-layout>

          <v-divider></v-divider>

          <v-layout v-if="Object.keys(page.boat.equipment.headers).length != 0" row wrap class="py-2">
            <v-flex xs12>
              <v-card>
                <v-card-title class="cyan darken-3 white--text">
                  <h2 class="title mb-0">{{ $t('boatPage.equipment') }}</h2>
                </v-card-title>
                <v-card-text>
                  <span class="body-2">{{ $t('boatPage.equipmentdesc') }}</span>
                </v-card-text>
                <v-card-text class="pa-1">
                  <v-expansion-panel focusable expand popout>
                    <v-expansion-panel-content  v-for="(equipment, index) in page.boat.equipment.headers" :key="index">
                      <div slot="header">{{ index }}</div>
                      <v-card>
                        <v-card-text>
                          <span class="body-2" v-for="(item, index) in equipment" :key="item.EquipmentTypeID">
                              {{ item.Name }}<span v-show="index + 1 !== equipment.length">,</span>
                          </span>
                        </v-card-text>
                      </v-card>
                    </v-expansion-panel-content>
                  </v-expansion-panel>
                </v-card-text>
              </v-card>
            </v-flex>

          </v-layout>

          <v-divider></v-divider>

          <v-layout row wrap class="py-2">
            <v-flex xs12>
              <v-card>
                <v-card-title class="cyan darken-3 white--text">
                  <h2 class="title mb-0">{{ $t('boatPage.charterprices') }}</h2>
                </v-card-title>
                <v-card-text>
                  <div class="body-1">Click on a date to swap your charter dates!</div>
                </v-card-text>
              </v-card>
            </v-flex>

            <v-flex xs12 class="calendar">
              <transition-group name="slide-y-transition" tag="v-layout" class="row wrap">
                <v-flex v-if="showMonth(index, month)" xs12 sm6 md4 v-for="(month, index) in page.boat.periods_calendar.months" :key="index">
                  <v-card>
                    <v-card-title class="cyan darken-3 white--text">
                      <h6 class="subheading ma-0 pa-0">{{ index }}</h6>
                    </v-card-title>
                    <v-card-text>
                      <ul>
                        <li 
                          @click="selectPeriod(period)" 
                          :class="{ 
                            'primary': some(booking.periods, {'activeDate': period.nice_date}),
                            'disabled': (period.AvailabilityStatus === 3) ? true : false
                          }" 
                          class="item body-2 pa-1" 
                          v-for="period in month" 
                          :key="period.nice_date"
                        >
                          <v-tooltip bottom>
                            <span slot="activator">
                              <span>{{ period.nice_date }}</span>
                              <span class="price">


                                <span v-if="period.PriceWithDiscount" class="dis">
                                  <span v-if="(period.BasePrice - period.PriceWithDiscount) / period.BasePrice * 100 == 0">
                                    -5%
                                  </span>
                                  <span v-else>
                                    -{{ Math.round((period.BasePrice - period.PriceWithDiscount) / period.BasePrice * 100) }}%
                                  </span>
                                  <price style="float: left;" :class="(period.PriceWithDiscount) ? 'body-2' : 'caption'" 
                                    currency="EUR" 
                                    :value="period.PriceWithDiscount"></price>
                                </span>
                              </span>
                            </span>
                            <span>
                              <div v-show="period.AvailabilityStatus === 3">
                                Reserve to find live availability
                              </div>
                              <div v-show="period.AvailabilityStatus !== 3">
                                Boat has live availability
                              </div>
                            </span>
                          </v-tooltip>
                        </li>
                      </ul>
                    </v-card-text>
                  </v-card>
                </v-flex>
              </transition-group>
            </v-flex>
          </v-layout>

          <v-divider></v-divider>

          <!-- optional extras -->
          <v-layout v-if="page.boat.extras.length > 0" row wrap class="py-2">
            <v-flex xs12>
              <v-card>
                <v-card-title class="cyan darken-3 white--text">
                  <h2 class="title mb-0">{{ $t('boatPage.extras') }}</h2>
                </v-card-title>
                <v-card-text>
                  <p>{{ $t('boatPage.extraspaid') }}</p>
                  <p>{{ $t('boatPage.extrasfree') }}</p>
                </v-card-text>
                <v-card-text class="pt-0">
                  <v-layout row wrap>
                    <v-flex xs12 sm4 v-if="extra.BoatExtraPrice[0]" v-for="extra in page.boat.extras" :key="extra.ExtraID">
                      {{ extra.BoatExtraPrice[0].Name }}
                      <span v-show="extra.BoatExtraPrice[0].ApproximatePrice != 0">{{ extra.BoatExtraPrice[0].price }}*</span>
                      <span v-show="extra.BoatExtraPrice[0].IncludedInCharterPrice != 0">{{ $t('utility.free') }}**</span>
                    </v-flex>
                  </v-layout>
                </v-card-text>
              </v-card>
            </v-flex>
          </v-layout>

          <v-divider></v-divider>

          <v-layout row wrap class="py-2">
            <v-flex xs12>
              <review :reviews="page.boat.reviews" :boat_id="page.boat.ID"></review>
            </v-flex>
          </v-layout>

          <v-divider></v-divider>

          <!-- google maps -->
          <v-layout>
            <v-flex xs12>
              <v-card>
                <v-card-title class="cyan darken-3 white--text">
                  <h2 class="title mb-0">{{ $t('boatPage.map') }}</h2>
                </v-card-title>
                <v-card-text>   
                  <gmap-map
                    :center="{lat:parseFloat(page.boat.location.coordinates.lat), lng: parseFloat(page.boat.location.coordinates.lng)}"
                    :zoom="16"
                    map-type-id="roadmap"
                    style="width: 100%; height: 400px">
                      <gmap-marker 
                        :position="{lat:parseFloat(page.boat.location.coordinates.lat), lng: parseFloat(page.boat.location.coordinates.lng)}" 
                        ></gmap-marker>
                  </gmap-map>
                </v-card-text>
              </v-card>
            </v-flex>
          </v-layout>
        </v-flex>

        <v-flex xs12 md4>
          <booking :isMobile="isMobile" :booking="booking" :page="page"></booking>
          <!-- TODO: implement all dates and sat to sat for countries :state.search="state.search" -->
        </v-flex>

      </v-layout>
    </v-container>
    <pages-card 
      v-if="page.pages.sailingItineraries" 
      :pages="page.pages.sailingItineraries"
      :title="$t('blogCard.itineraries', { location: page.boat.location.country_name })"
      :s3Url="`${s3}uploads/sailing-itineraries/`"
    ></pages-card>

    <blog-card :wp="page.wp" :location="page.boat.location.country_name"></blog-card>

    <v-snackbar
      :timeout="timeout"
      bottom
      vertical
      v-model="snackbar"
    >
      {{ text }}
      <v-btn flat color="pink" @click.native="snackbar = false">{{ $t('utility.close') }}</v-btn>
    </v-snackbar>

  </section>
</template>

<style scoped>
.layout-specs h2 {
  margin-bottom: 0px;
}
ul {
  list-style: none;
  padding-left: 0px;
}
.calendar .price {
  float: right;
}
.calendar .item {
  cursor: pointer;
}

.calendar .active {
  background: rgba(0,0,0,0.4);
}

.price .dis {
  border: 1px solid red;
  border-radius: 30%;
  padding: 3px;
  margin-left: 10px;
}
.disabled {
  background: #ff5252;
}
</style>

<script>
import Price from '~/components/Price'
import Review from '~/components/Review'
import Booking from '~/components/Booking'
import BlogCard from '~/components/BlogCard'
import PagesCard from '~/components/PagesCard'
import { some, each } from 'lodash'
import MetaService from '~/services/meta'

export default {
  components: {
    Price, Review, Booking, BlogCard, PagesCard
  },
  transition: {
    name: 'page',
    mode: 'out-in'
  },
  methods: {
    /**
     * @description idea is simple, show the previous, current and next month when selecting charter prices 
     * TODO this is a mess
    */
    showMonth (index, month) {
      const date = new Date(index)
      const currentBookingDate = new Date(this.booking.date)
      // TODO fix this crap
      if (currentBookingDate.getMonth() - 1 === date.getMonth() && currentBookingDate.getYear() === date.getYear()) {
        return true
      } else if (currentBookingDate.getMonth() + 1 === date.getMonth() && currentBookingDate.getYear() === date.getYear()) {
        return true
      } else if (currentBookingDate.getMonth() === date.getMonth() && currentBookingDate.getYear() === date.getYear()) {
        return true
      } else {
        return false
      }
    },
    some (p, b) {
      return some(p, b)
    },
    setBooking (date, weeks) {
      let count = 0
      let periods = []
      this.booking.price = 0
      this.booking.basePrice = 0
      this.booking.date = date
      // get the periods
      each(this.page.boat.periods, (value, index) => {
        const periodDate = value.From
        if (count !== 0 && count < weeks) {
          periods.push(value)
          count++
        }
        if (periodDate.split('T')[0] === this.booking.date) {
          periods.push(value)
          count++
        }
      })

      // create a combined price and discount etc
      each(periods, (period, index) => {
        this.booking.price += period.PriceWithDiscount
        this.booking.basePrice += period.BasePrice
        periods[index].activeDate = period.From.split('T')[0]
      })
      this.booking.periods = periods

      this.booking.discount = Math.round((this.booking.basePrice - this.booking.price) / this.booking.basePrice * 100)
    },
    selectPeriod (period) {
      this.setBooking(period.nice_date, this.booking.weeks)
    }
  },
  async asyncData ({ params, store, isMobile, app, route, error }) {
    try {
      let page = {}
      let boat = params.id.split('_')
      let boatID = boat[boat.length - 1]
      let activePeriod = false

      let { data } = await app.$axios.get('/api/v1/pages/boat/' + boatID)
      page = data

      let breadcrumbs = [
        {
          '@id': process.env.BASE_URL.slice(0, -1) + app.i18n.path('/'),
          name: 'Home'
        },
        {
          '@id': process.env.BASE_URL.slice(0, -1) + app.i18n.path('/search/'),
          name: 'Search'
        },
        {
          '@id': process.env.BASE_URL.slice(0, -1) + app.i18n.path('search' + page.boat.location.urls[0]),
          name: page.boat.location.country_name
        },
        {
          '@id': process.env.BASE_URL.slice(0, -1) + app.i18n.path('search' + page.boat.location.urls[1]),
          name: page.boat.location.area_name
        },
        {
          '@id': process.env.BASE_URL.slice(0, -1) + app.i18n.path('search' + page.boat.location.urls[2]),
          name: page.boat.location.base_name
        },
        {
          '@id': process.env.BASE_URL.slice(0, -1) + app.i18n.path(app.i18n.hreflang(route.fullPath)),
          name: page.boat.ModelName
        }
      ]

      if (store.state.search && store.state.search.date) {
        each(page.boat.periods, (value, index) => {
          const periodDate = value.From
          if (periodDate.split('T')[0] === store.state.search.date) {
            activePeriod = value
          }
        })
      }

      if (activePeriod === false) { // use the first available period
        activePeriod = page.boat.periods[0]
      }

      activePeriod.dateFormatted = activePeriod.From.split('T')[0]
      activePeriod.activeDate = activePeriod.dateFormatted

      const d = new Date()

      return {
        page: page,
        booking: {
          periods: [activePeriod],
          date: (store.state.search.date && new Date(store.state.search.date) >= d.setHours(0, 0, 0, 0)) ? store.state.search.date : d.toISOString().split('T')[0],
          price: activePeriod.PriceWithDiscount,
          basePrice: activePeriod.BasePrice,
          discount: Math.round((activePeriod.BasePrice - activePeriod.PriceWithDiscount) / activePeriod.BasePrice * 100),
          weeks: 1
        },
        breadcrumbs: breadcrumbs,
        isMobile: isMobile,
        baseUrl: process.env.BASE_URL,
        snackbar: false,
        timeout: 10000,
        text: '',
        s3: process.env.s3.url
      }
    } catch (e) {
      error({ statusCode: 404, message: 'Page not found' })
    }
  },
  data () {
    return {
      isMobile: false,
      offsetTop: 0
    }
  },
  mounted () {

  },
  created () {
    const that = this
    setTimeout(() => {
      that.snackbar = true

      that.text = that.$t('boatPage.urgency')
    }, 20000)
  },
  head () {
    const breadcrumbsJSON = MetaService.breadcrumbsJSON(this.breadcrumbs)

    let images = ''
    const image = (this.page.boat.images.length > 0) ? 'https://s3.eu-west-2.amazonaws.com/sc30/uploads/boats/' + this.page.boat.images[0].url : ''
    if (this.page.boat.images.length > 0) {
      for (const image of this.page.boat.images) {
        images += `"${`https://s3.eu-west-2.amazonaws.com/sc30/uploads/boats/${image.url}`}",`
      }
    }
    const productJSON = MetaService.productJSON(
      this.$t('boatPage.title', { model: this.page.boat.ModelName, location: this.page.boat.location.base_name }),
      images,
      this.$t('boatPage.description', {
        buildyear: this.page.boat.BuildYear,
        modelname: this.page.boat.ModelName,
        cabins: this.page.boat.Cabins,
        location: this.page.boat.location.base_name,
        country: this.page.boat.location.country_name,
        berths: this.page.boat.Berths
      }),
      this.page.boat.BrandName,
      this.page.boat.periods[0].PriceWithDiscount
    )

    return {
      title: this.$t('boatPage.title', { model: this.page.boat.ModelName, location: this.page.boat.location.base_name, country: this.page.boat.location.country_name }),
      meta: [
        { hid: 'description',
          name: 'description',
          content: this.$t('boatPage.description', {
            buildyear: this.page.boat.BuildYear,
            modelname: this.page.boat.ModelName,
            cabins: this.page.boat.Cabins,
            location: this.page.boat.location.base_name,
            country: this.page.boat.location.country_name,
            berths: this.page.boat.Berths
          })
        },
        { hid: 'twittertitle', name: 'twitter:title', content: this.$t('boatPage.title', { model: this.page.boat.ModelName, location: this.page.boat.location.base_name, country: this.page.boat.location.country_name }) },
        { hid: 'twitterdescription',
          name: 'twitter:description',
          content: this.$t('boatPage.description', {
            buildyear: this.page.boat.BuildYear,
            modelname: this.page.boat.ModelName,
            cabins: this.page.boat.Cabins,
            location: this.page.boat.location.base_name,
            country: this.page.boat.location.country_name,
            berths: this.page.boat.Berths
          })
        },
        { hid: 'twittercreator', name: 'twitter:creator', content: 'Chris Lait' },
        { hid: 'twitterimg', name: 'twitter:image:src', content: image },
        { hid: 'ogtitle', property: 'og:title', content: this.$t('boatPage.title', { model: this.page.boat.ModelName, location: this.page.boat.location.base_name, country: this.page.boat.location.country_name }) },
        { hid: 'ogtype', property: 'og:type', content: 'product' },
        { hid: 'ogurl', property: 'og:url', content: this.$route.path },
        { hid: 'ogimage', property: 'og:image', content: image },
        { hid: 'ogdescription',
          property: 'og:description',
          content: this.$t('boatPage.description', {
            buildyear: this.page.boat.BuildYear,
            modelname: this.page.boat.ModelName,
            cabins: this.page.boat.Cabins,
            location: this.page.boat.location.base_name,
            country: this.page.boat.location.country_name,
            berths: this.page.boat.Berths
          })
        }
      ],
      script: [
        { innerHTML: breadcrumbsJSON, type: 'application/ld+json' },
        { innerHTML: productJSON, type: 'application/ld+json' }
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