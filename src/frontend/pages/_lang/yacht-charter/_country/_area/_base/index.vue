<template>
  <section>
    <v-parallax class="yacht-charter text-xs-center" v-if="data.page" :src="s3 + s3Loc + data.page.pageImage[0].fileName" height="400">
      <v-layout
        column
        align-center
        justify-center
        class="white--text"
      >
        <h1 v-if="data.page.title" class="white--text display-3">{{ data.page.title }}</h1>
        <div v-if="data.page.subtitle" class="subheading hidden-xs-only mb-3 display-1 text-xs-center">{{ data.page.subtitle }}</div>
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
    <v-parallax v-else
      title="Picturesque Sunset" 
      alt="Picturesque sunset in Croatia, one of the top yacht charter destinations" 
      src="https://s3.eu-west-2.amazonaws.com/sc30/static/sailchecker_marina_sunset.jpg" height="600">
      <v-layout
        column
        align-center
        justify-center
        class="white--text"
      >
        <h1 class="white--text display-3">{{ $t('yachtCharterPage.meta.title', { location: data.location.name }) }}</h1>
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

    <v-container v-if="data.page">
      <v-layout row wrap>
        <v-flex class="main-content" v-if="data.page.body" xs12 md8>
          <div v-html="data.page.body"></div>
        </v-flex>
        <v-flex xs12 md8 v-else>
          <div class="body-2">
            {{ $t('yachtCharterPage.noContent', { location: data.location.name }) }}
          </div>
        </v-flex>
        <v-flex v-if="data.boats.length" class="pb-5" xs12 md4>
          <single-boat-card
            :title="$t('yachtCharterPage.topBoatHeader', { location: data.location.name })" 
            :boat="data.boats[0]"/>
        </v-flex>
      </v-layout>
    </v-container>

    <chequer
      v-if="data.page && data.page.pageChequer" 
      :chequers="data.page.pageChequer" 
      :s3Content="s3 + s3Loc"
    ></chequer>

    <v-carousel class="mb-3" v-if="data.page && data.page.pageImage">
      <v-carousel-item
        v-for="(image, i) in data.page.pageImage"
        :key="i"
        :src="s3 + s3Loc + image.fileName"
        transition="fade"
        reverseTransition="fade"
      ></v-carousel-item>
    </v-carousel>

    <v-container class="cyan darken-3 text-xs-center elevation-10" v-if="data.boats.length > 1" fluid>
      <v-layout>
        <v-flex xs12>
          <h3 class="white--text">{{ $t('yachtCharterPage.boatsHeader', { location: data.location.name }) }}</h3>
        </v-flex>
      </v-layout>
    </v-container>
    <v-container>
      <v-layout row wrap>
        <multi-boat-card searchpage="false" :boats="data.boats.slice(1)"/>
      </v-layout>
    </v-container>

    <pages-card 
      v-if="data.pages.sailingItineraries" 
      :pages="data.pages.sailingItineraries"
      :title="$t('blogCard.itineraries', { location: data.location.name })"
      :s3Url="`${s3}uploads/sailing-itineraries/`"
    ></pages-card>

    <blog-card :wp="data.wp" :location="data.location.name"></blog-card>

    <v-container class="cyan darken-3 elevation-10" fluid>
      <v-layout>
        <v-flex xs12>
          <h3 class="white--text text-xs-center">{{ $t('yachtCharterPage.topDestinations') }}</h3>
        </v-flex>
      </v-layout>
    </v-container>

    <popular-locations :locations="data.topDestinations"/>

    <v-snackbar
      :timeout="timeout"
      bottom
      vertical
      v-model="snackbar"
    >
      {{ text }}
      <v-layout>
        <nuxt-link class="pull-left orange--text" :to="this.$i18n.path(searchPath)">
          <v-btn flat color="orange">{{ $t('utility.search') }}</v-btn>
        </nuxt-link>
        <v-spacer></v-spacer>
        <v-btn flat color="pink" @click.native="snackbar = false">{{ $t('utility.close') }}</v-btn>
      </v-layout>
    </v-snackbar>

  </section>
</template>

<script>
import SingleInputSearch from '~/components/SingleInputSearch'
import MultiBoatCard from '~/components/MultiBoatCard'
import SingleBoatCard from '~/components/SingleBoatCard'
import PopularLocations from '~/components/PopularLocations'
import BlogCard from '~/components/BlogCard'
import Chequer from '~/components/Chequer'
import PagesCard from '~/components/PagesCard'
import Enquire from '~/components/Enquire'
import MetaService from '~/services/meta'

export default {
  transition: {
    name: 'page',
    mode: 'out-in'
  },
  components: {
    SingleInputSearch,
    MultiBoatCard,
    SingleBoatCard,
    PopularLocations,
    PagesCard,
    BlogCard,
    Chequer,
    Enquire
  },
  async asyncData ({ params, app, breadcrumbs, error }) {
    try {
      const { data } = await app.$axios.get('api/v1/pages/yachtCharter', { params })

      const searchPath = '/search/'

      return {
        data: data,
        s3: process.env.s3.url,
        s3Loc: process.env.s3.yachtCharterContent,
        params: params,
        searchPath: searchPath,
        breadcrumbs: breadcrumbs
      }
    } catch (e) {
      error({ statusCode: 404, message: 'Page not found' })
    }
  },
  created () {
    setTimeout(() => {
      this.snackbar = true

      this.text = this.$t('yachtCharterPage.urgency', {
        name: (this.$store.state.user.name) ? this.$store.state.user.name : '',
        number: Math.floor((Math.random() * 3) + 2),
        location: this.data.location.name
      })
    }, 10000)
  },
  data () {
    // set the data for simple search component
    if (this.$route.params.hasOwnProperty('area')) {
      this.$store.state.search.region = '/' + this.$route.params.country + '/' + this.$route.params.area + '/'
    } else if (this.$route.params.hasOwnProperty('country')) {
      this.$store.state.search.region = '/' + this.$route.params.country + '/'
    }

    return {
      title: '',
      boats: [],
      destinations: [],
      topDestinations: [],
      location: false,
      description: '',
      body: '',
      page: {},
      baseUrl: process.env.BASE_URL,
      snackbar: false,
      timeout: 10000,
      text: ''
    }
  },
  head () {
    const breadcrumbsJSON = MetaService.breadcrumbsJSON(this.breadcrumbs)
    const metaDetails = {
      // default meta details
      creator: 'Chris Lait',
      type: 'article',
      url: this.$i18n.path(this.$route.path)
    }

    if (!this.data.page) {
      metaDetails.title = this.$t('yachtCharterPage.meta.title', { location: this.data.location.name })
      metaDetails.description = this.$t('yachtCharterPage.meta.description', { location: this.data.location.name })
      metaDetails.image = 'https://s3.eu-west-2.amazonaws.com/sc30/static/sailchecker_marina_sunset.jpg'
    } else {
      metaDetails.title = this.data.page.title
      metaDetails.description = this.data.page.description
      metaDetails.image = this.s3 + this.s3Loc + this.data.page.pageImage[0].fileName
    }

    const meta = MetaService.base(metaDetails)

    return {
      title: metaDetails.title,
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

