<template>
  <v-app
    light
    id="inspire"
  >
    <my-header/>
    <main>
      <v-content>
        <nuxt/>
      </v-content>
    </main>
    <my-footer/>
  </v-app>
</template>

<script>
import MyHeader from '~/components/Header.vue'
import MyFooter from '~/components/Footer.vue'

export default {
  components: {
    MyFooter,
    MyHeader
  }
}
</script>