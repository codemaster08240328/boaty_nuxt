<template>
  <v-footer height="auto" class="cyan darken-3">
    <v-container grid-list-xs fluid pt-0 pb-0>
      <v-card flat tile>
        <v-card-title class="cyan darken-3 white--text text-xs-center">
          <strong class="title mb-4">Get connected with us on social networks!</strong>
          <v-spacer></v-spacer>
          <a class="mx-3" href="https://www.facebook.com/sailChecker1/" target="_blank">
            <v-icon large color="white">fa fa-facebook</v-icon>
          </a>
          <a class="mx-3" href="https://twitter.com/sailchecker" target="_blank">
            <v-icon large color="white">fa fa-twitter</v-icon>
          </a>
          <a class="mx-3" href="https://www.instagram.com/sailchecker/" target="_blank">
            <v-icon large color="white">fa fa-instagram</v-icon>
          </a>
          <a class="mx-3" href="https://plus.google.com/+Sailchecker" target="_blank">
            <v-icon large color="white">fa fa-google-plus</v-icon>
          </a>
          <a class="mx-3" href="https://www.pinterest.com/drakesail/sail-checker/" target="_blank">
            <v-icon large color="white">fa fa-pinterest-p</v-icon>
          </a>
        </v-card-title>
        <v-card-text class="cyan darken-3">
          <v-layout row wrap>
            <v-flex class="white--text mb-4" xs12 md4>
              <v-layout row wrap>
                <v-flex xs12>
                  <span class="title">
                    SailChecker
                  </span>
                </v-flex>
              </v-layout>
              <v-layout row wrap>
                <v-flex xs10>
                  <p>Yacht Charter is a passport to the unrivalled riches of our planet; SailChecker is your guide to a collection of carefully selected Charter Yachts, Catamarans, and Motor Yachts; Skippered, Crewed and Bareboat, worldwide.</p>
                </v-flex>
              </v-layout>
            </v-flex>
            <v-flex class="white--text mb-4" xs12 md4>
              <span class="title my-1">USEFUL LINKS</span>
              <div class="ma-1" v-for="links in usefulLinks" :key="links.url">
                <a :href="links.url">{{ links.name }}</a>
              </div>
            </v-flex>
            <v-flex class="white--text mb-4" xs12 md4>
              <span class="title my-1">CONTACT</span>
              <div class="ma-1">
                <v-icon color="white" size="24px" class="mr-3">fa fa-home</v-icon>
                Kemp House, 152 City Road, London EC1V 2NX
              </div>              
              <div class="ma-1">
                <v-icon color="white" size="24px" class="mr-3">fa fa-envelope</v-icon>
                <a href="mailto:info@sailchecker.com">enquire@sailchecker.com</a>
              </div>
              <div class="ma-1">
                <v-icon color="white" size="24px" class="mr-3">fa fa-phone</v-icon>
                <a href="tel:+44 8000 988 118">UK: +44 8000 988 118</a>
              </div>
              <div class="ma-1">
                <v-icon color="white" size="24px" class="mr-3">fa fa-phone</v-icon>
                <a href="tel:+1-844-335-1306">US: +1-844-335-1306</a>
              </div>
              <div class="ma-1">
                <v-icon color="white" size="24px" class="mr-3">fa fa-phone</v-icon>
                <a href="tel:+30 800 848 1290">GR: +30 800 848 1290</a>
              </div>
              <div class="ma-1">
                <v-icon color="white" size="24px" class="mr-3">fa fa-phone</v-icon>
                <a href="tel:+61 730678907">AU: +61 730678907</a>
              </div>
            </v-flex>
          </v-layout>
        </v-card-text>
        <!-- <v-card-actions class="cyan darken-3 white--text pt-3 justify-center">
          &copy;2012 - 2018 — <strong>SailChecker</strong>
          <nuxt-link v-if="$i18n.locale === 'en-us'" :to="`/gb` + $route.fullPath">
            {{ $t('links.english') }}
          </nuxt-link>
          <nuxt-link v-else :to="$route.fullPath.replace(/^\/[^\/]+/, '')" active-class="none" exact>
            {{ $t('links.american') }}
          </nuxt-link>
        </v-card-actions> -->
      </v-card>
    </v-container>
  </v-footer>
</template>

<style scoped>
  a {
    text-decoration: none;
    color: white;
  }
  h4 {
    margin-bottom: 0px;
  }
</style>
<script>
export default {
  data () {
    let links = [
      {
        url: `${process.env.BASE_URL}meet-the-crew`,
        name: 'Meet The Crew'
      },
      {
        url: `${process.env.BASE_URL}sailing-itineraries`,
        name: 'Sailing Itineraries'
      },
      {
        url: `${process.env.BASE_URL}bareboat-charter`,
        name: 'Bareboat Charter'
      },
      {
        url: `${process.env.BASE_URL}catamaran-charter`,
        name: 'Catamaran Charter'
      },
      {
        url: `${process.env.BASE_URL}luxury-yacht-charter`,
        name: 'Luxury Yacht Charter'
      },
      {
        url: `${process.env.BASE_URL}contact-us`,
        name: 'Contact Us'
      },
      {
        url: `${process.env.BASE_URL}sitemap.xml`,
        name: 'Sitemap'
      }
    ]
    return {
      usefulLinks: links
    }
  }
}
</script>
