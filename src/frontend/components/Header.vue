<template>
  <header id="header">
    <v-navigation-drawer
      clipped
      v-model="drawer"
      app
      hide-overlay
      disable-route-watcher
      stateless
    >
      <v-list>
        <v-list-tile v-for="item in items" :key="item.text">
          <v-list-tile-action>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <div v-if="item.offsite">
              <nuxt-link :to="$i18n.path(item.path)">
                <v-list-tile-title>
                  {{ item.text }}
                </v-list-tile-title>
              </nuxt-link>
            </div>
            <div v-else>
              <a :href="item.path">
                <v-list-tile-title>
                  {{ item.text }}
                </v-list-tile-title>
              </a>
            </div>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-navigation-drawer>

    <v-toolbar 
      class="cyan darken-3" 
      prominent
      fixed
      clipped-left
      app
      light>
      <v-container grid-list-xs>
        <v-layout class="ml-4" row align-content-end align-center>
          <v-toolbar-title>
            <v-toolbar-side-icon class="white--text pull-left" @click.stop="drawer = !drawer"></v-toolbar-side-icon>
          </v-toolbar-title>
          <nuxt-link style="cursor: pointer;" class="header-home mr-3" :to="$i18n.path('/')">
            <img height="50px" alt="SailChecker Logo" title="SailChecker Logo" src="https://s3.eu-west-2.amazonaws.com/sc30/static/sailchecker_logo_icon_only.svg"/>
          </nuxt-link>
          <!-- <v-btn :to="'/enquiry'" large class="white--text teal darken-4">Get A QUOTE</v-btn> -->
          <v-spacer></v-spacer>
          <v-btn class="hidden-sm-and-down" dark flat color="white" style="cursor: pointer;" :to="$i18n.path('/bareboat-charter/')">Bareboat Charter</v-btn>
          <v-btn class="hidden-sm-and-down" dark flat color="white" style="cursor: pointer;" :to="$i18n.path('/catamaran-charter/')">Catamaran Charter</v-btn>
          <v-dialog 
            v-model="dialog"
            class="hidden-sm-and-down"
            fullscreen
            hide-overlay
            transition="dialog-bottom-transition">
            <v-btn color="white" dark flat slot="activator">Sailing Itineraries</v-btn>
            <v-card>
              <v-toolbar class="cyan darken-3">
                <v-container grid-list-xs>
                  <v-layout align-center row>
                      <v-btn icon dark @click.native="dialog = false">
                        <v-icon>close</v-icon>
                      </v-btn>
                      <v-toolbar-title class="white--text">Sailing Itineraries</v-toolbar-title>
                  </v-layout>
                </v-container>
              </v-toolbar>
              <v-container grid-list-md>
                <v-layout row wrap>
                  <v-flex d-flex child-flex v-for="(itinerary, index) in $store.state.sailingItineraries" :key="index">
                    <v-card>
                      <v-card-media :src="`https://s3.eu-west-2.amazonaws.com/sc30/uploads/sailing-itineraries/medium-${itinerary[0].pageImage[0].fileName}`" height="100px">
                      </v-card-media>
                      <v-card-title class="pa-1">
                        <h3 class="headline mb-0">{{ index }}</h3>
                      </v-card-title>
                      <v-card-text class="pa-1">
                        <div v-for="page in itinerary" :key="page.id">
                          <nuxt-link @click.native="dialog = false" :to="$i18n.path(page.link)">{{ page.title }}</nuxt-link>
                        </div>
                      </v-card-text>
                    </v-card>        
                  </v-flex>
                </v-layout>
              </v-container>
            </v-card>
          </v-dialog>
          <v-menu class="hidden-sm-and-down" open-on-hover bottom transition="slide-y-transition" offset-y>
            <v-btn color="white" dark flat slot="activator">{{ c_currency }}<v-icon>keyboard_arrow_down</v-icon></v-btn>
            <v-list>
              <v-list-tile v-for="currency in currencies" :key="currency" @click="selectCurrency (currency)">
                <v-list-tile-title>{{ currency }}</v-list-tile-title>
              </v-list-tile>
            </v-list>
          </v-menu>
          <a href="https://www.trustpilot.com/review/sailchecker.com" rel="nofollow" target="_blank">
            <img class="trustpilot" src="https://s3.eu-west-2.amazonaws.com/sc30/static/trust-pilot-logo.png" alt="TrustPilot" title="TrustPilot" />
          </a>
        </v-layout>
      </v-container>
    </v-toolbar>
  </header>
</template>

<script>
import SingleInputSearch from '~/components/SingleInputSearch'

export default {
  components: {
    SingleInputSearch
  },
  data () {
    return {
      c_currency: this.$store.state.defaultCurrency,
      currencies: Object.keys(this.$store.state.fx.rates),
      drawer: false,
      yachtCharterDialog: false,
      dialog: false,
      items: [
        // { icon: 'place', text: 'Destinations', offsite: true, path: '/yacht-charter' },
        { icon: 'book', text: 'Our Blog', offsite: false, path: 'https://sailchecker.com/all-things-sailing-blog' },
        { icon: 'directions_boat', text: 'Bareboat Charter', offsite: false, path: 'https://sailchecker.com/bareboat-charter' },
        { icon: 'directions_boat', text: 'Catamaran Charter', offsite: false, path: 'https://sailchecker.com/catamaran-charter' },
        { icon: 'directions_boat', text: 'Luxury Yacht Charter', offsite: false, path: 'https://sailchecker.com/luxury-yacht-charter' },
        { icon: 'directions_boat', text: 'Yacht Charter', offsite: false, path: 'https://sailchecker.com/yacht-charter' },
        { icon: 'list', text: 'Sailing Itineraries', offsite: false, path: 'https://sailchecker.com/sailing-itineraries' },
        { icon: 'people', text: 'Meet The Crew', offsite: false, path: 'https://sailchecker.com/meet-the-crew' },
        { icon: 'call', text: 'Contact Us', offsite: false, path: 'https://sailchecker.com/contact-us' }
      ]
    }
  },
  methods: {
    selectCurrency (currency) {
      this.c_currency = currency
      this.$store.commit('setDefaultCurrency', currency)
    }
  }
}
</script>
<style scoped>
.header-home {
  text-decoration: none;
}
.trustpilot {
  height: 50px;
}
</style>