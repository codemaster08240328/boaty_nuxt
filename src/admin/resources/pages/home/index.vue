<template>
  <section>
    <v-container fluid>
      <v-layout row wrap>
        <v-flex xs 12>
          <v-alert color="info" icon="info" v-model="alerts">
            Welcome {{ $store.state.authUser.full_name }}, this is the CMS for SailChecker's search and directories.</br>
            Click on the links below to Manage Content, Manage Bookings, Add Reviews and Update Boat Related Info
          </v-alert>
        </v-flex>

        <v-flex xs12>
          <nuxt-link tag="v-btn" to="/home/content">
            Manage Content
          </nuxt-link>
          <nuxt-link tag="v-btn" to="/home/bookings">
            Manage Bookings & Requests
          </nuxt-link>
          <nuxt-link tag="v-btn" to="/home/reviews">
            Manage Reviews
          </nuxt-link>
          <nuxt-link tag="v-btn" to="/home/boats">
            Update Boat Info
          </nuxt-link>
        </v-flex>
      </v-layout>
    </v-container>
  </section>
</template>


<script>
export default {
  middleware: 'auth',
  data () {
    return {
      alerts: true
    }
  }
}
</script>