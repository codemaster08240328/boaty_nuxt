<template>
  <v-container
    class="pa-0" 
    fluid>
    <v-layout 
      :id="`chequer-${chequer.id}`"
      v-for="(chequer, index) in chequers" 
      :key="chequer.id" 
      :class="{ 'row-reverse': index % 2 !== 0 }" 
      row wrap>
      <v-flex style="overflow: hidden;" xs12 md6>
        <v-parallax height="600" :alt="chequer.altText" :title="chequer.titleText" :src="s3Content + 'chequer-' + chequer.fileName"></v-parallax>
      </v-flex>
      <v-flex xs12 md6 class="cyan darken-3">
        <v-container fill-height>
          <v-container class="chequer-width" style="border: 1px solid white;">
            <v-layout row wrap>
              <v-flex text-xs-center xs12>
                <h2 class="display-1 pb-4 white--text">{{ chequer.title }}</h2>
              </v-flex>
              <v-flex xs12>
                <div class="white--text body-1 chequer-body" v-html="chequer.body"></div>
              </v-flex>
              <v-flex text-xs-center xs12>
                <enquire></enquire>
              </v-flex>
            </v-layout>
          </v-container>
        </v-container>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
import Enquire from '~/components/Enquire'

export default {
  components: {
    Enquire
  },
  props: [
    'chequers',
    's3Content'
  ]
}
</script>