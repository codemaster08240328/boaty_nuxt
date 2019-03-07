<template>
  <section>
    <v-container fluid>
      <v-layout row wrap>
        <v-flex xs 12>
          <v-alert color="info" icon="info" v-model="alerts">
            Boats from our API proviers aren't always perfect, if we have a popular boat, we can add content and images here
          </v-alert>
        </v-flex>
      </v-layout>
    </v-container>
  </section>
</template>

<script>
export default {
  data () {
    return {
      alerts: true
    }
  }
}
</script>
