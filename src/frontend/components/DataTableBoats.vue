<template>
  <section>
    <v-data-table
      :headers="headers"
      :items="boats"
      :pagination.sync="pagination"
      hide-actions
      item-key="ID"
      expand
      class="striped"
    >
      <template slot="items" slot-scope="props">
        <!-- v-show="(props.index === 0) ? props.expanded = true && true : true" -->
        <tr :class="`row-${props.index}`" @click="expand(props)">
          <td class="hidden-sm-and-down">
            <img 
              height="32"
              width="32"
              :src="'https://s3.eu-west-2.amazonaws.com/sc30/uploads/boats/thumbnail-' + props.item.boatModelImagePrimary"
            />
          </td>
          <td>
            {{ props.item.ModelName }}
          </td>
          <td class="text-xs-center">{{ props.item.BuildYear }}</td>
          <td class="text-xs-center">{{ props.item.Cabins }}</td>
          <td class="hidden-sm-and-down text-xs-center">{{ props.item.Toilets }}</td>
          <td class="hidden-sm-and-down text-xs-center">{{ props.item.base_name }}</td>
          <td class="text-xs-center"><price currency="EUR" :value="props.item.boatPriceWithDiscount"></price></td>
          <td class="hidden-sm-and-down text-xs-center">
            <span v-if="props.item.rating > 9.9">
              10
            </span>
            <span v-else-if="props.item.rating < 6.5">
              6.5
            </span>
            <span v-else>
              {{ props.item.rating }}
            </span>
          </td>
          <td class="hidden-sm-and-down"><v-icon>keyboard_arrow_down</v-icon></td>
        </tr>
      </template>
      <template slot="expand" slot-scope="props">
        <v-layout row wrap>
          <v-flex xs12>
            <v-card class="cyan darken-3 white--text">
              <v-container fluid grid-list-md>
                <v-layout row wrap>
                  <v-flex xs5 md3>
                    <v-card-media
                      :src="'https://s3.eu-west-2.amazonaws.com/sc30/uploads/boats/thumbnail-' + props.item.boatModelImagePrimary"
                      height="125px"
                      contain
                    ></v-card-media>
                    <div class="text-xs-center mt-2">
                      <v-chip color="red" text-color="white" v-if="props.item.boatAvailabilityStatus === 3">HOT RIGHT NOW</v-chip>
                      <v-chip color="cyan" text-color="white" v-if="props.item.boatAvailabilityStatus === 2">UNDER OFFER!</v-chip>
                    </div>
                    <div class="text-xs-center">
                      <nuxt-link class="white--text" :to="{ path: $i18n.path(props.item.countryUrl), query: queryparams }">{{ props.item.country_name }}</nuxt-link>
                      >
                      <nuxt-link class="white--text" :to="{ path: $i18n.path(props.item.areaUrl), query: queryparams }">{{ props.item.area_name }}</nuxt-link>
                      >
                      <nuxt-link class="white--text" :to="{ path: $i18n.path(props.item.baseUrl), query: queryparams }">{{ props.item.base_name }}</nuxt-link>
                    </div>
                  </v-flex>
                  <v-flex xs7 md2>
                    <div>
                      <div class="body-2">{{ props.item.Name }}, {{ props.item.ModelName }}, {{ props.item.BrandName }}, {{ props.item.TypeName }}</div>
                      <div class="body-1">
                        Berths: {{ props.item.Berths }} <br>
                        Toilets: {{ props.item.Toilets }} <br>
                        LOA: {{ props.item.OverallLength }} <br>
                        Cabins: {{ props.item.Cabins }} <br>
                        Year: {{ props.item.BuildYear }}<br>
                      </div>
                    </div>
                  </v-flex>
                  <v-flex xs12 md7 class="elevation-25 white--text text-xs-center">
                    <span class="body-1">
                      SailChecker's Best Price Guaranteed
                    </span>
                    <span class="display-2">
                      <price class="elevation-25" currency="EUR" :value="props.item.boatPriceWithDiscount"></price>
                    </span>
                    <span class="body-1 orange--text">
                      {{ $t('searchPage.recentViews', { views: randomViews() }) }}
                    </span>
                    <v-card-actions class="white--text">
                      <v-spacer></v-spacer>
                      <enquire :boat="props.item" :buttonTitle="'Ask A Question'"></enquire>
                      <br>
                      <v-btn large class="white--text cyan darken-2" :to="{ path: $i18n.path(props.item.boatUrl), query: queryparams }">
                        {{ $t('utility.details') }}
                      </v-btn>
                      <v-spacer></v-spacer>
                    </v-card-actions>
                  </v-flex>
                </v-layout>
              </v-container>
            </v-card>
          </v-flex>
        </v-layout>
      </template>
      <template slot="no-data">
        <v-alert :value="true" color="error" icon="warning">
          Sorry, we don't have any vessels that match your criteria. Please reach out to our yacht charter consultants via live chat for help.
        </v-alert>
      </template>
    </v-data-table>
  </section>
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
    'boats'
  ],
  methods: {
    randomViews () {
      return Math.floor(Math.sqrt(Math.random() * 100 ^ 2))
    },
    randomRequests () {
      return Math.floor(Math.sqrt(Math.random() * 10 ^ 2))
    },
    expand (props) {
      console.log('testing', !props.expanded)
      props.expanded = !props.expanded
      console.log(props.expanded)
      return true
    }
  },
  created () {
    if (process.browser) {
      setTimeout(() => {
        document.querySelector('.row-0').click()
      }, 1000)
    }
    return true
  },
  computed: {
    queryparams () {
      return this.$store.getters.queryparams
    }
  },
  data () {
    return {
      pagination: {
        sortBy: 'rating',
        descending: true,
        rowsPerPage: -1
      },
      headers: [
        {
          text: '',
          align: 'left',
          sortable: false,
          class: 'hidden-sm-and-down'
        },
        {
          text: 'Boat',
          align: 'left',
          sortable: false,
          value: 'ModelName'
        },
        { text: 'Year', align: 'center', value: 'BuildYear' },
        { text: 'Cabins', align: 'center', value: 'Cabins' },
        { text: 'WCs', align: 'center', width: '20px', class: 'hidden-sm-and-down', value: 'Toilets' },
        { text: 'Port', align: 'center', class: 'hidden-sm-and-down', value: 'base_name' },
        { text: 'Price', value: 'boatPriceWithDiscount' },
        { text: 'SC Rating', class: 'hidden-sm-and-down', align: 'center', value: 'rating' },
        { text: ' ', sortable: false, class: 'hidden-sm-and-down', value: 'expand' }
      ]
    }
  }
}
</script>