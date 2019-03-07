<template>
  <section>
    <!-- header -->
    <v-parallax 
    class="yacht-charter text-xs-center" 
    v-if="data.page && data.page.pageImage.length > 0" 
      :src="s3 + `uploads/${originalPageType}/` + data.page.pageImage[0].fileName" height="400">
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

    <v-breadcrumbs id="breadcrumbs" justify-center>
      <v-icon slot="divider">forward</v-icon>
      <v-breadcrumbs-item :href="breadcrumb['@id']" v-for="breadcrumb in breadcrumbs" :key="breadcrumb['@id']">
        {{ breadcrumb.name }}
      </v-breadcrumbs-item>
    </v-breadcrumbs>

    <!-- main content & sidebar -->
    <v-container v-if="data.page">
      <v-layout row wrap>
        <v-flex class="main-content" v-if="data.page.body" xs12 md8>
          <div v-html="data.page.body"></div>

          <v-layout class="group-links" justify-center align-center row wrap v-for="(pageLinks, index) in data.pageLinks" :key="index">
            <v-flex class="text-xs-center" xs12>
              <h3 class="headline px-0 py-2 ma-0">{{ pageLinks.name }}</h3>
            </v-flex>
            <v-flex v-if="pageLinks.previous" class="text-xs-center" xs6>
              <v-btn :to="$i18n.path(pageLinks.previous.page.link)" class="cyan darken-2 white--text" flat large>
                <span v-if="pageLinks.previous.title">
                  {{ pageLinks.previous.title }}
                </span>
                <span v-else>
                  {{ pageLinks.previous.page.title }}
                </span>
              </v-btn>
            </v-flex>
            <v-flex  v-if="pageLinks.next" class="text-xs-center" xs6>
              <v-btn v-if="pageLinks.next" :to="$i18n.path(pageLinks.next.page.link)" class="cyan darken-2 white--text" raised large>
                <span v-if="pageLinks.next.title">
                  {{ pageLinks.next.title }}
                </span>
                <span v-else>
                  {{ pageLinks.next.page.title }}
                </span>
              </v-btn>
            </v-flex>
          </v-layout>
        </v-flex>
        <!-- sidebar -->
        <v-flex class="pb-5" xs12 md4>
          <v-container grid-list-md fluid>
            <v-layout row wrap>
              <v-flex v-if="data.boats.length > 1" xs12>
                <single-boat-card
                  v-if="data.location"
                  :title="$t('yachtCharterPage.topBoatHeader', 
                    { location: 
                      (data.location.name.charAt(data.location.name.length - 1) === 's' ) 
                        ? data.location.name.slice(0, data.location.name.length - 1)
                        : data.location.name
                    }
                  )" 
                  :boat="data.boats[0]"/>
                <single-boat-card
                  v-else-if="!data.location"
                  :title="$t('yachtCharterPage.topBoatHeaderNoLocation')"
                  :boat="data.boats[0]"
                  />
              </v-flex>
              
              <v-flex xs12 v-if="data.pages.sailingItineraries.length > 0">
                <v-card>
                  <v-card-title class="cyan darken-3 white--text">
                    <div>
                      <div class="title">{{ $t('page.relatedItineraries') }}</div>
                    </div>
                  </v-card-title>
                  <v-card-text>
                    <div class="mb-1" v-for="(item) in data.pages.sailingItineraries" :key="item.id">
                      <nuxt-link :to="$i18n.path(item.link)">{{ item.title }}</nuxt-link>
                    </div>
                  </v-card-text>
                </v-card>
              </v-flex>
            </v-layout>
          </v-container>
        </v-flex>
      </v-layout>
    </v-container>

    <v-container>
      <v-layout row wrap>
        <v-flex xs12>
          <chequer-links v-if="data.page.pageChequer" :chequers="data.page.pageChequer" />
        </v-flex>
      </v-layout>
    </v-container>

    <chequer 
      v-if="data.page.pageChequer" 
      :chequers="data.page.pageChequer" 
      :s3Content="s3Content"
    ></chequer>

    <!-- <v-carousel v-if="data.page.pageImage.length > 1">
      <v-carousel-item
        v-for="(image, i) in data.page.pageImage"
        :key="i"
        :src="s3Content + image.fileName"
        transition="fade"
        reverseTransition="fade"
      ></v-carousel-item>
    </v-carousel> -->

    <v-container>
      <v-layout row wrap>
        <v-flex offset-sm3 sm6 xs12>
          <div data-width="100%" class="fb-comments" :data-href="'https://sailchecker.com' + $route.fullPath" data-numposts="5"></div>
        </v-flex>
      </v-layout>
    </v-container>

    <full-width-page-title 
      v-if="data.location"
      :title="$t('yachtCharterPage.boatsHeader', { location: data.location.name })"
    ></full-width-page-title>
    <full-width-page-title 
      v-else
      :title="$t('yachtCharterPage.boatsHeaderNoLocation')"
    ></full-width-page-title>

    <v-container>
      <v-layout row wrap>
        <multi-boat-card searchpage="false" :boats="data.boats.slice(1)"/>
      </v-layout>
    </v-container>

    <!-- itineraries -->
    <pages-card 
      v-if="data.region && data.pages.sailingItineraries" 
      :pages="data.pages.sailingItineraries"
      :title="$t('blogCard.itineraries', { location: data.location.name })"
      :s3Url="`${s3}uploads/sailing-itineraries/`"
    ></pages-card>

    <!-- blogs -->
    <pages-card 
      v-if="data.region && data.pages.blogs" 
      :pages="data.pages.blogs"
      :title="$t('blogCard.blogs', { location: data.location.name })"
      :s3Url="`${s3}uploads/blogs/`"
    ></pages-card>

  </section>
</template>

<script>
import SingleInputSearch from '~/components/SingleInputSearch'
import MultiBoatCard from '~/components/MultiBoatCard'
import PopularLocations from '~/components/PopularLocations'
import PagesCard from '~/components/PagesCard'
import Enquire from '~/components/Enquire'
import MetaService from '~/services/meta'
import SingleBoatCard from '~/components/SingleBoatCard'
import Chequer from '~/components/Chequer'
import ChequerLinks from '~/components/ChequerLinks'
import FullWidthPageTitle from '~/components/FullWidthPageTitle'

export default {
  transition: {
    name: 'page',
    mode: 'out-in'
  },
  components: {
    SingleInputSearch,
    SingleBoatCard,
    MultiBoatCard,
    PopularLocations,
    PagesCard,
    Enquire,
    Chequer,
    ChequerLinks,
    FullWidthPageTitle
  },
  async asyncData ({ params, app, breadcrumbs, error, route, store }) {
    try {
      const routeParts = route.fullPath.split('/')
      let originalPageType = ''
      let pageType = ''
      if ((routeParts[routeParts.length - 1]) === '') {
        originalPageType = routeParts[routeParts.length - 3]
      } else {
        originalPageType = routeParts[routeParts.length - 2]
      }

      const singular = [
        'blog'
      ]

      if (originalPageType.includes(singular)) {
        pageType = `${originalPageType}s`
      }

      let { data } = await app.$axios.get(`/api/v1/pages/blog/${originalPageType}/${params.slug}`)

      return {
        data: data,
        originalPageType: originalPageType,
        pageType: pageType,
        s3: process.env.s3.url,
        s3Content: process.env.s3.url + `uploads/${originalPageType}/`,
        breadcrumbs: breadcrumbs,
        baseUrl: process.env.BASE_URL
      }
    } catch (e) {
      error({ statusCode: 404, message: 'Page not found' })
    }
  },
  mounted () {
    if (!process.server) {
      if (window.FB) {
        window.FB.XFBML.parse()
      }
    }
  },
  data () {
    return {}
  },
  head () {
    const breadcrumbsJSON = MetaService.breadcrumbsJSON(this.breadcrumbs)
    const data = this.data

    const meta = MetaService.base({
      title: data.page.title,
      description: data.page.description,
      image: (data.page.pageImage) ? this.s3Content + data.page.pageImage[0].fileName : false,
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

