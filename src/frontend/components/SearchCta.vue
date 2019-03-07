<template>
  <section>
    <v-card class="mt-4" v-if="page.description">
      <v-card-media v-if="page.pageImage.length !== 0"
        class="white--text"
        height="200px"
        :src="s3 + 'uploads/search/medium-' + page.pageImage[0].fileName">
      </v-card-media>
      <v-card-title>
        <h3>{{ $t('searchPage.guide', { title: title }) }}</h3>
        <div class="body-2" style="white-space: pre-line;" v-html="page.description"></div>
      </v-card-title>
      <v-card-actions>
        <nuxt-link :to="$i18n.path('/')">{{ $t('searchPage.learn', { title: title }) }}</nuxt-link>
      </v-card-actions>
    </v-card>

    <v-card dense class="mt-4">
      <v-card-actions>
        <v-list three-line dense>
          <v-list-tile avatar>
            <v-list-tile-action>
              <v-icon color="green" class="display-1">money_off</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title class="subheading">{{ $t('searchPage.moneyofftitle') }}</v-list-tile-title>
              <div>{{ $t('searchPage.moneyoff') }}</div>
            </v-list-tile-content>
          </v-list-tile>

          <v-list-tile avatar>
            <v-list-tile-action>
              <v-icon color="blue" class="display-1">credit_card</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title class="subheading">{{ $t('searchPage.securetitle') }}</v-list-tile-title>
              <div>{{ $t('searchPage.secure') }}</div>
            </v-list-tile-content>
          </v-list-tile>

          <v-list-tile avatar>
            <v-list-tile-action>
              <v-icon color="orange" class="display-1">event_available</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title class="subheading">{{ $t('searchPage.availabletitle') }}</v-list-tile-title>
              <div>{{ $t('searchPage.available') }}</div>
            </v-list-tile-content>
          </v-list-tile>
        </v-list>
      </v-card-actions>
    </v-card>
  </section>
</template>

<script>
export default {
  props: [
    'page',
    'title',
    's3',
    's3Loc'
  ]
}
</script>