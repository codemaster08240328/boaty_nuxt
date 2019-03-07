<template>
  <section v-if="wp">
    <v-container grid-list-md fluid v-if="wp.sailing_itinerary">
      <v-layout class="cyan darken-3 elevation-10">
        <v-flex xs12>
          <h3 class="white--text text-xs-center pa-2">{{ $t('blogCard.itineraries', { location: location }) }}</h3>
        </v-flex>
      </v-layout>

      <v-layout row wrap>
        <v-container>
          <v-layout row wrap>
            <v-flex xs12 md3 v-for="item in wp.sailing_itinerary" v-bind:key="item.id">
              <v-card>
                <v-card-media v-if="item.image !== false" :src="item.image" height="200px">
                </v-card-media>
                <v-card-title primary-title>
                  <div>
                    <h3 class="headline mb-0">{{ item.title }}</h3>
                    <div>{{ item.seo_meta.description }}</div>
                  </div>
                </v-card-title>
                <v-card-actions>
                  <a :href="item.link">
                    <v-btn flat color="red">
                      {{ $t('blogCard.readmore') }}
                    </v-btn>
                  </a>
                </v-card-actions>
              </v-card>
            </v-flex>
          </v-layout>
        </v-container>
      </v-layout>
    </v-container>

    <v-container grid-list-md fluid v-if="wp.blog">
      <v-layout class="cyan darken-3 elevation-10">
        <v-flex xs12>
          <h3 class="white--text text-xs-center pa-2">{{ $t('blogCard.blogs', { location: location }) }}</h3>
        </v-flex>
      </v-layout>

      <v-layout row wrap>
        <v-container>
          <v-layout row wrap>
            <v-flex xs12 md3 v-for="item in wp.blog" v-bind:key="item.id">
              <v-card>
                <v-card-media v-if="item.image !== false" :src="item.image" height="200px">
                </v-card-media>
                <v-card-title primary-title>
                  <div>
                    <h3 class="headline mb-0">{{ item.title }}</h3>
                    <div>{{ item.seo_meta.description }}</div>
                  </div>
                </v-card-title>
                <v-card-actions>
                  <a :href="item.link">
                    <v-btn flat color="red">
                      {{ $t('blogCard.readmore') }}
                    </v-btn>
                  </a>
                </v-card-actions>
              </v-card>
            </v-flex>
          </v-layout>
        </v-container>
      </v-layout>
    </v-container>
  </section>
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
    'wp',
    'location'
  ],
  data () {
    return {}
  }
}
</script>
