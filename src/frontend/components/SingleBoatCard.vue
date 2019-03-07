<template>
  <v-card :id="boat.Name">
    <v-card-title class="cyan darken-3 white--text headline">
      {{ title }}
    </v-card-title>
    <v-card-media
      class="white--text"
      height="200px"
      :src="'https://s3.eu-west-2.amazonaws.com/sc30/uploads/boats/thumbnail-' + boat.boatModelImagePrimary"
    >
      <v-container fill-height fluid>
        <v-layout fill-height>
          <v-flex xs12>
            <span class="title">{{ boat.Name }}</span>
          </v-flex>

          <v-flex xs12>
            <v-chip color="red" text-color="white" v-if="boat.boatAvailabilityStatus === 3">HOT RIGHT NOW</v-chip>
            <v-chip color="cyan" text-color="white" v-if="boat.boatAvailabilityStatus === 2">UNDER OFFER!</v-chip>
          </v-flex>
        </v-layout>
      </v-container>
    </v-card-media>
    <v-card-title>
      <v-layout row wrap>
        <v-flex xs12>
          <div class="body-2 orange--text">
            {{ $t('searchPage.recentViews', { views: randomViews() }) }}
            </div>
        </v-flex>
        <v-flex xs12 sm6>
          <span class="grey--text"><b>{{ boat.Name }}</b>, {{ boat.ModelName }}, {{ boat.BrandName }}, {{ boat.TypeName }}</span><br>
          <span class="grey--text"><nuxt-link :to="$i18n.path(boat.countryUrl)">{{ boat.country_name }}</nuxt-link></span> > 
          <span class="grey--text"><nuxt-link :to="$i18n.path(boat.areaUrl)">{{ boat.area_name }}</nuxt-link></span> > 
          <span class="grey--text"><nuxt-link :to="$i18n.path(boat.baseUrl)">{{ boat.base_name }}</nuxt-link></span><br>
            <!-- {{ boat.boat_currency }}{{ boat.boatPriceWithDiscount }}</span> -->
        </v-flex>
        <v-flex xs12 sm6>
          <div>
            Berths: {{ boat.Berths }} <br>
            Toilets: {{ boat.Toilets }} <br>
            LOA: {{ boat.OverallLength }} <br>
            Cabins: {{ boat.Cabins }} <br>
            BuildYear: {{ boat.BuildYear }}<br>
          </div>
        </v-flex>
        <v-flex xs12>
          <span class="blue--text text-xs-center display-2">
            <price currency="EUR" :value="boat.boatPriceWithDiscount"></price>
          </span>
        </v-flex>
      </v-layout>
    </v-card-title>
    <v-card-actions>
      <enquire :boat="boat" :buttonTitle="''"></enquire>
      <nuxt-link color="cyan" :to="$i18n.path(boat.boatUrl)">
        <v-btn flat>
          {{ $t('utility.details') }}
        </v-btn>
      </nuxt-link>
    </v-card-actions>
  </v-card>
</template>

<script>
import Price from '~/components/Price'
import Enquire from '~/components/Enquire'

export default {
  components: {
    Price,
    Enquire
  },
  props: [
    'title',
    'boat'
  ],
  data () {
    return {}
  },
  methods: {
    randomViews () {
      return Math.floor(Math.sqrt(Math.random() * 100 ^ 2))
    },
    randomRequests () {
      return Math.floor(Math.sqrt(Math.random() * 10 ^ 2))
    }
  }
}
</script>

