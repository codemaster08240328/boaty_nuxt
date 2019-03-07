<template>
  <section>
    <v-breadcrumbs id="breadcrumbs" justify-center>
      <v-icon slot="divider">forward</v-icon>
      <v-breadcrumbs-item :href="breadcrumb['@id']" v-for="breadcrumb in breadcrumbs" :key="breadcrumb['@id']">
        {{ breadcrumb.name }}
      </v-breadcrumbs-item>
    </v-breadcrumbs>

    <!-- <div v-for="(name, i) in Object.keys(data.groups)" :key="i">
      <a :href="`#${name}`">{{ name }}</a>
    </div> -->
    <div v-for="(item, i) in data.groups" :key="i">
      <pages-card
        :id="i"
        :pages="item" 
        :title="$t('blogCard.title', { location: i, type: pageType })"
        :s3Url="`${s3}uploads/${originalPageType}/`"
      ></pages-card>
    </div>
  </section>
</template>

<style>

</style>

<script>
import PagesCard from '~/components/PagesCard'
import MetaService from '~/services/meta'

export default {
  transition: {
    name: 'page',
    mode: 'out-in'
  },
  components: {
    PagesCard
  },
  async asyncData ({ params, app, breadcrumbs, error, route }) {
    try {
      const routeParts = route.fullPath.split('/')

      let originalPageType = ''
      let pageType = ''
      if ((originalPageType = routeParts[routeParts.length - 1]) === '') {
        originalPageType = routeParts[routeParts.length - 2]
      }

      const plural = [
        'blog'
      ]

      if (originalPageType.includes(plural)) {
        pageType = `${originalPageType}s`
      } else {
        pageType = originalPageType
      }

      let { data } = await app.$axios.get(`/api/v1/pages/blog/${originalPageType}`)

      return {
        data: data,
        originalPageType: originalPageType,
        pageType: pageType.charAt(0).toUpperCase() + pageType.slice(1).replace(/-/g, ' '), // used for plurals
        s3: process.env.s3.url,
        s3Content: process.env.s3.url + process.env.s3.sailingItineraryContent,
        breadcrumbs: breadcrumbs
      }
    } catch (e) {
      error({ statusCode: 404, message: 'Page not found' })
    }
  },
  head () {
    const breadcrumbsJSON = MetaService.breadcrumbsJSON(this.breadcrumbs)
    console.log(this.pageType)
    const meta = MetaService.base({
      title: this.$t('page.title', { type: this.pageType }),
      description: this.$t('page.description', { type: this.pageType }),
      image: (this.data.groups) ? this.s3 + `uploads/${this.originalPageType}/` + this.data.groups[Object.keys(this.data.groups)[0]][0].pageImage[0].fileName : false,
      creator: 'Chris Lait',
      type: 'article',
      url: this.$i18n.path(this.$route.path)
    })

    return {
      title: this.$t('page.title', { type: this.pageType }),
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

