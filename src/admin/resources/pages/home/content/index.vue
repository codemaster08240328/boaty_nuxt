<template>
  <v-container fluid>
    <v-layout row wrap>
      <v-flex xs12>
        <nuxt-link tag="v-btn" class="button" to="/home/content/yacht-charter">
          Yacht Charter content (includes search pages)
        </nuxt-link>

        <nuxt-link tag="v-btn" to="/home/content/popular-locations">
          Change Popular Countries (locations)
        </nuxt-link>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
export default {

}
</script>
