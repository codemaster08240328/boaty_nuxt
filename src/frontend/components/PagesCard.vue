<template>
  <div v-if="pages.length > 0">
    <v-container class="white--text text-xs-center cyan darken-3 elevation-10 py-3 mb-2" grid-list-md fluid>
      <v-layout row wrap>
        <v-flex xs12>
          <h3 class="pa-2 headline">{{ title }}</h3>
        </v-flex>
      </v-layout>
    </v-container>

    <v-container grid-list-md>
      <v-layout row wrap>
        <v-flex xs12 md3 v-for="item in pages" v-bind:key="item.id">
          <v-card>
            <v-card-media 
              v-if="item.pageImage.length > 0" 
              :src="s3Url + 'medium-' + item.pageImage[0].fileName" 
              :alt="item.pageImage[0].altText" 
              :title="item.pageImage[0].titleText"
              height="200px">
            </v-card-media>
            <v-card-title primary-title>
              <div>
                <h3 class="headline mb-0">{{ item.title }}</h3>
                <div>{{ item.description }}</div>
              </div>
            </v-card-title>
            <v-card-actions>
              <nuxt-link :to="$i18n.path(item.link)">
                <v-btn flat color="red">
                  {{ $t('blogCard.readmore') }}
                </v-btn>
              </nuxt-link>
            </v-card-actions>
          </v-card>
        </v-flex>
      </v-layout>
    </v-container>
  </div>
</template>

<style scoped>
  a {
    text-decoration: none;
  }
  h3 {
    text-transform: capitalize;
  }
</style>

<script>
export default {
  props: [
    'pages',
    'title',
    's3Url'
  ]
}
</script>
