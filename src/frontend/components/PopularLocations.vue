<template>
  <v-container class="topDestinations py-3" grid-list-md>
    <v-layout row wrap>
      <v-flex xs12 sm4
        v-for="destination in locations"
        :key="destination.title">
        <nuxt-link :to="$i18n.path(destination.url)">
          <v-card ripple hover>
            <v-card-media
              v-if="destination.fileName"
              class="white--text"
              height="200px"
              :src="s3 + s3Loc + 'medium-' + destination.fileName"
            >
            </v-card-media>
            <v-card-title>
              <div>
                <h4 class="headline">{{ destination.title }}</h4>
                <div class="body-2 grey--text p-1 boatCount">
                  {{ $t('utility.boats', { boat_count: destination.boat_count }) }}
                </div>
              </div>
            </v-card-title>
          </v-card>
        </nuxt-link>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<style scoped>
.topDestinations a {
  text-decoration: none !important;
}
</style>

<script>
export default {
  props: [
    'locations'
  ],
  data () {
    return {
      s3: process.env.s3.url,
      s3Loc: process.env.s3.yachtCharterContent
    }
  }
}
</script>
