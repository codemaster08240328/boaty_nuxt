<template>
  <v-container class="white--text text-xs-center cyan darken-3 elevation-10 py-3 mb-2" grid-list-md fluid>
    <v-layout row wrap>
      <v-flex xs12>
        <h3 class="pa-2 headline">{{ title }}</h3>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
export default {
  props: [
    'title'
  ]
}
</script>
