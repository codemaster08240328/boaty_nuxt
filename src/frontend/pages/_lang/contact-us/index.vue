<template>
  <section>
    
<!-- The first one offers 3 options Like
Which lead to forms Like
Where I want ot use the second layout Like
SOme reasons why to get in touch Like
Plus we get a click on the site Like
oh okay, so no form? Like -->

    <v-parallax
      class="yacht-charter"
      title="Yacht moored offshore in greece" 
      alt="Yacht moored offshore in greece" 
      src="https://s3-eu-west-2.amazonaws.com/sc30/uploads/2018/02/19122606/ionian-sailing-day-four-sailing-yacht.jpg" 
      height="300">
      <v-layout
        column
        align-center
        justify-center
        class="white--text"
      >
        <!-- <img src="https://s3.eu-west-2.amazonaws.comReach out to our Sales team directly for immediate assistance with all sales-related inquiries./sailchecker_logo_icon_only.svg" 
          alt="SailChecker Logo" title="SailChecker"> -->
        <h1 class="white--text mb-2 display-1 text-xs-center">Send Us Your Enquiry</h1>
        <h2 style="width: 80%;" class="title mb-3 text-xs-center">Our experienced consultants are here to respond to every single question and help you pick a suitable sailing holiday.</h2>
      </v-layout>
      <v-layout justify-center align-center class="parallax-psp hidden-sm-and-down">
        <!-- <v-flex xs2 offset-xs1>
          <div class="mb-1">
            <h4 class="body-1">Our global partners</h4>
          </div>
          <div class="items">
            <div style="width: 0px; height: 100px;"></div>
            <img class="mx-1" width="100" src="https://s3.eu-west-2.amazonaws.com/sc30/static/payment/pantaenius.png"/>
          </div>
        </v-flex> -->
          <div class="items">
            <h4 class="body-1">SailChecker in the press</h4>
            <img 
              style="max-width: 600px;" 
              src="https://s3.eu-west-2.amazonaws.com/sc30/static/awards/in-the-press-min.png" 
              alt="SailChecker's appearances in press"
            />
          </div>

      </v-layout>
    </v-parallax>

    <v-breadcrumbs id="breadcrumbs" class="mb-0 pb-0" justify-center>
      <v-icon slot="divider">forward</v-icon>
      <v-breadcrumbs-item :href="breadcrumb['@id']" v-for="breadcrumb in breadcrumbs" :key="breadcrumb['@id']">
        {{ breadcrumb.name }}
      </v-breadcrumbs-item>
    </v-breadcrumbs>

    <v-container>
      <v-layout row wrap class="text-xs-center mt-4">
        <v-flex xs12 class="mb-4">
          <h2 class="cyan--text text--darken-4">Reasons For Enquiry</h2>
        </v-flex>

        <v-flex xs12 offset-md2 md4>
          <i style="font-size: 80px;" class="material-icons cyan--text text--darken-3">call</i>
          <h3 class="cyan--text text--darken-4">Charter Enquiry</h3>
          <p class="body-1">
            Send us your charter enquiry today and we will make sure that we send the best available options within 24 hours.
          </p>
          <v-btn round large class="cyan darken-2 white--text my-4" :to="$i18n.path('enquiry')">Charter Enquiry</v-btn>
        </v-flex>
        <v-flex xs12 md4>
          <i style="font-size: 80px;" class="material-icons cyan--text text--darken-3">live_help</i>
          <h3 class="cyan--text text--darken-4">Contact A Consultant</h3>
          <p class="body-1">
            Need Help? Our Experienced Consultants would love to assist.
          </p>
          <v-btn round large class="cyan darken-2 white--text my-4" :to="$i18n.path('meet-the-crew')">Contact A Consultant</v-btn>
        </v-flex>
      </v-layout>
    </v-container>

    <v-container fluid grid-list-md>
      <v-layout row wrap>
        <v-flex xs12 offset-sm2 sm6>
          <v-layout row wrap>
            <v-flex xs12 sm6>
              <h2 class="cyan--text text--darken-4">Corporate Headquaters</h2>
              <p>
                SailChecker Ltd <br>
                Kemp House, 152 City Road<br>
                London, EC1V 2NX
              </p>
            </v-flex>
            <v-flex xs12 sm6>
              <h2 class="cyan--text text--darken-4">General Contact</h2>
              <p>
                Phone: <a href="tel:+44 330 808 2207">+44 330 808 2207</a> <br>
                Email: <a href="enquire@sailchecker.com">enquire@sailchecker.com</a>
              </p>
            </v-flex>
            <v-flex xs12 sm6>
              <h2 class="cyan--text text--darken-4">Our Bankers</h2>
              <p>
                <b>Barclays Banks</b> <br>
                Canary Wharf <br>
                London, E14 5HP <br>
                IBAN GB14 BARC <br>
                20201513990168 <br>
                <b>SWIFTBIC</b> BARCGB22
              </p>
            </v-flex>
            <v-flex xs12 sm6>
              <h2 class="cyan--text text--darken-4">International Contact</h2>
              <p>
                London <a href="tel:+44 330 808 2207">+44 330 808 2207</a> <br>
                United States <a href="tel:+1-888-683-6572 ">+1-888-683-6572 </a> <br>
                Greece <a href="tel:+1-888-683-6572 ">+1-888-683-6572 </a> <br>
                Australia <a href="tel:+61-312235122">+61-312235122</a>
              </p>
            </v-flex>
          </v-layout>
        </v-flex>
        <v-flex xs12 sm4>
          <gmap-map
            :center="{lat:51.627226, lng:-0.091959}"
            :zoom="10"
            style="width: 100%; height: 300px">

            <gmap-info-window class="cyan darken-3" :options="infoOptions" :position="infoWindowPos">
              <v-container fluid grid-list-md>
                <v-layout justify-center align-center class="cyan--text text--darken-3 subheading">
                  <i class="material-icons">place</i>
                  <h3>SailChecker Headquaters</h3>
                </v-layout>
                <v-layout row wrap>
                  <v-flex xs12 class="ml-4">
                    <p class="body-2">Kemp House <br> 152 City Road <br> London <br> EC1V 2NX</p>
                    <!-- <a href="" target="_blank">Get Directions</a> -->
                  </v-flex>
                </v-layout>
              </v-container>
            </gmap-info-window>
          </gmap-map>
        </v-flex>
      </v-layout>
    </v-container>
  </section>
</template>

<style scoped>

</style>

<script>
// import { some, each } from 'lodash'
import MetaService from '~/services/meta'

export default {
  transition: {
    name: 'page',
    mode: 'out-in'
  },
  async asyncData ({ breadcrumbs, error }) {
    try {
      return {
        breadcrumbs: breadcrumbs
      }
    } catch (e) {
      error({ statusCode: 404, message: 'Page not found' })
    }
  },
  data () {
    return {
      infoWindowPos: {lat: 51.5271104, lng: -0.089212},
      infoContent: {},
      infoOptions: {
        pixelOffset: {
          width: 0,
          height: 0
        }
      }
    }
  },
  head () {
    const breadcrumbsJSON = MetaService.breadcrumbsJSON(this.breadcrumbs)

    const meta = MetaService.base({
      title: 'Contact Us',
      description: 'Send SailChecker your enquiries, we love to get your questions and requests, small things do matter.',
      image: 'https://s3-eu-west-2.amazonaws.com/sc30/uploads/2018/02/19122606/ionian-sailing-day-four-sailing-yacht.jpg',
      creator: 'Chris Lait',
      type: 'website',
      url: this.$i18n.path(this.$route.path)
    })

    return {
      title: 'Contact Us',
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