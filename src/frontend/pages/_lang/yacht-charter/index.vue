<template>
  <section>
    <v-parallax
      class="yacht-charter"
      title="Picturesque Sunset" 
      alt="Our Yacht Charter Fleet" 
      src="https://s3.eu-west-2.amazonaws.com/sc30/static/yacht-charter.jpg" height="600">
      <v-layout
        column
        align-center
        justify-center
        class="white--text"
      >
        <img src="https://s3.eu-west-2.amazonaws.com/sc30/static/sailchecker_logo_icon_only.svg" 
          alt="SailChecker Logo" title="SailChecker">
        <h1 class="white--text mb-2 display-1 text-xs-center">{{ $t('yachtCharterPage.title') }}</h1>
        <h2 class="hidden-xs-only title mb-3 text-xs-center">{{ $t('yachtCharterPage.introduction') }}</h2>
        <v-card style="border-radius: 10px; max-width: 600px; width:100%;" class="elevation-20 mb-2">
          <v-container color="cyan" grid-list-xs>
            <v-layout row wrap>
              <v-flex xs12>
                <single-input-search></single-input-search>
              </v-flex>
            </v-layout>
          </v-container>
        </v-card>
      </v-layout>
    </v-parallax>

    <v-breadcrumbs id="breadcrumbs" justify-center>
      <v-icon slot="divider">forward</v-icon>
      <v-breadcrumbs-item :href="breadcrumb['@id']" v-for="breadcrumb in breadcrumbs" :key="breadcrumb['@id']">
        {{ breadcrumb.name }}
      </v-breadcrumbs-item>
    </v-breadcrumbs>

    <v-container class="text-xs-center">
      <v-layout>
        <v-flex xs12>
          <h2 class="display-1">Top Yacht Charter Destinations</h2>
        </v-flex>
      </v-layout>
    </v-container>
    <popular-locations :locations="locations"/>


    <v-container>
      <v-layout row wrap>
        <v-flex xs12>
          <ul>
            <li v-for="country in destinations" v-bind:key="country.name">
              <nuxt-link :to="$i18n.path(country.url)">
                {{ country.name }}
              </nuxt-link>
              <ul>
                <li v-for="area in country.areas" v-bind:key="area.name">
                  <nuxt-link :to="$i18n.path(area.url)">
                    {{ area.name}}
                  </nuxt-link>
                  <ul>
                    <li v-for="base in area.bases" v-bind:key="base.name">
                      <nuxt-link :to="$i18n.path(base.url)">
                        {{ base.name }}
                      </nuxt-link>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
          </ul>
        </v-flex>
      </v-layout>
    </v-container>
  </section>
</template>

<script>
import SingleInputSearch from '~/components/SingleInputSearch'
import PopularLocations from '~/components/PopularLocations'
import MetaService from '~/services/meta'

export default {
  components: {
    SingleInputSearch,
    PopularLocations
  },
  data () {
    return {
      destinations: [],
      baseUrl: process.env.BASE_URL
    }
  },
  async asyncData ({ app, breadcrumbs, error }) {
    let destinations,
      markers,
      popularlocations

    try {
      let { data } = await app.$axios.get('/api/v1/seo/destinations')
      destinations = data
    } catch (e) {
      error({ statusCode: 404, message: 'Page not found' })
    }

    try {
      let { data } = await app.$axios.get('/api/v1/pages/home')
      markers = data.gmap
      popularlocations = data.popularLocations
    } catch (e) {
      error({ statusCode: 404, message: 'Page not found' })
    }

    return {
      destinations: destinations,
      markers: markers,
      locations: popularlocations,
      breadcrumbs: breadcrumbs
    }
  },
  head () {
    const breadcrumbsJSON = MetaService.breadcrumbsJSON(this.breadcrumbs)

    const meta = MetaService.base({
      title: this.$t('yachtCharterPage.title'),
      description: this.$t('yachtCharterPage.introduction'),
      image: 'https://s3.eu-west-2.amazonaws.com/sc30/static/yacht-charter.jpg',
      creator: 'Chris Lait',
      type: 'article',
      url: this.$i18n.path(this.$route.path)
    })

    return {
      title: this.$t('yachtCharterPage.title'),
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