<template>
  <section>
    <v-parallax
      class="yacht-charter"
      title="Picturesque Sunset" 
      alt="Picturesque sunset in Croatia, one of the top yacht charter destinations" 
      src="https://s3.eu-west-2.amazonaws.com/sc30/static/sailchecker_marina_sunset.jpg" height="380">
      <v-layout
        column
        align-center
        justify-center
        class="white--text"
      >
        <!-- <img src="https://s3.eu-west-2.amazonaws.com/sc30/static/sailchecker_logo_icon_only.svg" 
          alt="SailChecker Logo" title="SailChecker"> -->
        <h1 class="white--text mb-2 display-1 text-xs-center">{{ $t('home.title') }}</h1>
        <h2 class="hidden-xs-only title mb-3 text-xs-center">{{ $t('home.introduction') }}</h2>
        <v-card style="border-radius: 10px; max-width: 600px; width:100%;" class="elevation-20 mb-2">
          <v-container color="cyan" grid-list-xs>
            <v-layout row wrap>
              <v-flex xs12>
                <single-input-search></single-input-search>
              </v-flex>
            </v-layout>
          </v-container>
        </v-card>
        <div class="text-xs-center hidden-md-and-up mt-4">
          <img class="mx-1" width="100" src="https://s3.eu-west-2.amazonaws.com/sc30/static/awards/yy-award.png"/>
        </div>
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
    
    <v-container grid-list-xs fluid class="psp-bottom text-xs-center pb-3 mb-3 cyan darken-3 white--text">
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

    <v-container class="mb-3">
      <v-layout
        column
        align-center
        justify-center
        class="text-sm-center body-2"
      >
        <p class="pb-2">
          <b>Yacht Charter is a passport to the unrivalled riches of our planet;</b> SailChecker is your guide to a collection of carefully selected Charter Yachts, Catamarans, and Motor Yachts; Skippered, Crewed and Bareboat, worldwide.
        </p>
        <p>
          Our story begins attempting to navigate the bewildering range of yachts, charter, and terminology and a deep culture of high service standard. We are driven by a shared understanding of needing the careful blend of search technology with consistently high levels of customer service to realise the perfect sailing trip with total Peace of Mind.
        </p>
        <p>
          Our mission is to create your perfect sailing cruise, designed around your tastes, your dreams, and the way you choose to explore, play and relax. By routinely meeting with our partners, inspecting bases, and sailing on their yachts, my team and I can guide you to a tailored match for the kind of experience you have always imagined.
        </p>
        <p>
          Our Yacht Charter has no standard packages, we have no standard customers. Our collection is curated from bareboat yachts no more than 6 years, and crewed yachts of distinction. We look forward to creating your sailing dreams. 
        </p>
        <p class="">
          Yours sincerely,
        </p>
        <p class="">
          <i>
            Christopher Lait<br>
            CEO/Founder<br>
            SailChecker Ltd.
          </i>
        </p>
      </v-layout>
    </v-container>

    <full-width-page-title :title="'Top Yacht Charter Destinations'"></full-width-page-title>

    <popular-locations class="mb-2" :locations="locations"/>
    <v-container>
      <v-layout
        column
        align-center
        justify-center
        class="text-sm-center body-2"
      >
        <p>
          <b>BVI Yacht Charter.</b> Columbus was first to sight these islands and named them for the 11,000 virgins who followed Saint Ursula. Former haunt of famous  pirates, of whom Blackbeard is the most notorious. Today these islands are Nature's Little Secret, and  attract yachtsmen to their crystal clear waters, white sand beaches and promise of steady trade wind sailing. A developed infrastructure of restaurants and marinas, plus line-of-sight navigation between the 33-odd islands still make the British Virgin Islands (BVI) a sailor's paradise; windy, but not too windy, and never far from a rum punch. 
        </p>
        <p>
          <b>Croatia Yacht Charter.</b> The world's number one Yacht Charter Destination with its abundance of stunning islands,  National Parks, and exquisite cuisine. Sail into an intoxicating blend of ancient culture and civilisation adoring the magnificent Adriatic coastline - its azure waters and sheer natural beauty couldn't feel more different to its neighbours. The sheer variety of Croatia's 1000 islands, inlets and reefs make for some truly breathtaking anchorages as well as providing plenty of secluded spots to enjoy swimming and watersports.
        </p>
        <p>
          <b>Greece Yacht Charter.</b> They say Home is where the anchor drops, and with around 600 islands seemingly dropped by the gods themselves, picture postcard  surrounds at every turn await. Be prepared to sail under piercIng blue skies as the Aegean lap at an endless coastline with ancient sun-bleached ruins to admire. The Greek culture awash with inspired cuisine, passionate music, and legends galore. Each area offers different yet compelling sailing opportunities for all standards. Stroll along the picture perfect harbour of Hydra or visit a remote anchorage that can only be reached by water.
        </p>
      </v-layout>
    </v-container>

    <full-width-page-title :title="'SailChecker Challenges You!'"></full-width-page-title>

    <v-container grid-list-md>
      <v-layout row wrap>
        <v-flex class="py-3" xs12>
          <v-card>
            <v-card-media src="https://s3.eu-west-2.amazonaws.com/sc30/uploads/2013/03/Rolex-Racing.jpg" height="200px">
            </v-card-media>
            <v-card-title primary-title>
              <div>
                <h3 class="headline">SailChecker Racing Game</h3>
                <div>Do you think you can Sail? Do you think you can Race? Try out our new Sail Racing Challenge Game see if you can beat our CEO.</div>
              </div>
            </v-card-title>
            <v-card-actions>
              <a style="text-decoration: none;" target="_blank" href="https://sailchecker.com/sailchallenge/">
                <v-btn block large class="white--text" color="red">
                  Play Now!
                </v-btn>
              </a>
            </v-card-actions>
          </v-card>
        </v-flex>
      </v-layout>
    </v-container>

    <!-- google maps with markers and clusters begin -->
    <gmap-map
      :center="{lat:25.5372332, lng:15.043886}"
      :zoom="3"
      map-type-id="terrain"
      style="width: 100%; height: 500px">

      <gmap-info-window :options="infoOptions" :position="infoWindowPos" :opened="infoWinOpen" @closeclick="infoWinOpen=false">
        <v-container>
          <v-layout row wrap>
            <v-flex v-if="infoContent.base" xs12>
              <span class="headline">{{ infoContent.base }}</span>
            </v-flex>

            <v-flex xs12>
              <span v-if="infoContent.countryUrl" class="grey--text">
                <nuxt-link :to="$i18n.path(infoContent.countryUrl)">{{ infoContent.country }}</nuxt-link>
              </span> 
              <span v-if="infoContent.areaUrl" class="grey--text">
                > <nuxt-link :to="$i18n.path(infoContent.areaUrl)">{{ infoContent.area }}</nuxt-link>
              </span>
              <span v-if="infoContent.baseUrl" class="grey--text">
                > <nuxt-link :to="$i18n.path(infoContent.baseUrl)">{{ infoContent.base }}</nuxt-link>
              </span>
            </v-flex>
            <v-flex v-if="infoContent.count" xs12>
              <span class="subheading">{{ $t('googlemaps.numberofboats' )}} {{ infoContent.count }}</span>
            </v-flex>

            <v-flex v-if="infoContent.priceFrom" xs12>
              <nuxt-link :to="$i18n.path(infoContent.baseUrl)">
                View Yachts From €{{ infoContent.priceFrom }}
              </nuxt-link>
            </v-flex>

          </v-layout>
        </v-container>
      </gmap-info-window>
      <gmap-cluster>
        <gmap-marker 
          :key="i" 
          v-for="(m,i) in markers" 
          :position="m.position" 
          :clickable="true" 
          @click="toggleInfoWindow(m,i)"
          ></gmap-marker>
      </gmap-cluster>
    </gmap-map>
  </section>
</template>

<style scoped>
.why-us p {
  width: 80%
}
h3 {
  display: inline;
}
.psp-bottom i {
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
</style>

<script>
import SingleInputSearch from '~/components/SingleInputSearch'
import PopularLocations from '~/components/PopularLocations'
import Price from '~/components/Price'
import FullWidthPageTitle from '~/components/FullWidthPageTitle'

export default {
  components: {
    SingleInputSearch,
    PopularLocations,
    Price,
    FullWidthPageTitle
  },
  data () {
    return {
      infoContent: {},
      infoWindowPos: {
        lat: 0,
        lng: 0
      },
      infoWinOpen: false,
      currentMidx: null,
      // optional: offset infowindow so it visually sits nicely on top of our marker
      infoOptions: {
        pixelOffset: {
          width: 0,
          height: -35
        }
      },
      baseUrl: process.env.BASE_URL,
      markers: [],
      locations: [],
      modal: false,
      date: null
    }
  },
  async asyncData ({ app, error }) {
    let markers = []
    let popularlocations = []

    try {
      let { data } = await app.$axios.get('/api/v1/pages/home')
      markers = data.gmap
      popularlocations = data.popularLocations
    } catch (e) {
      error({ statusCode: 404, message: 'Page not found' })
    }

    return {
      markers: markers,
      locations: popularlocations
    }
  },
  methods: {
    toggleInfoWindow: function (marker, idx) {
      this.infoWindowPos = marker.position
      this.infoContent = marker
      // check if its the same marker that was selected if yes toggle
      if (this.currentMidx === idx) {
        this.infoWinOpen = !this.infoWinOpen
      } else {
        this.infoWinOpen = true
        this.currentMidx = idx
      }
    }
  },
  head () {
    return {
      link: [
        {
          hid: 'en',
          rel: 'alternate',
          href: this.baseUrl,
          hreflang: 'x-default'
        }
        // {
        //   hid: 'engb',
        //   rel: 'alternate',
        //   href: this.baseUrl + `gb/`,
        //   hreflang: 'en-gb'
        // }
      ]
    }
  }
}
</script>