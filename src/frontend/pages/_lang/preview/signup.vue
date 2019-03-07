<template>
  <v-container>
    <v-layout row wrap>
      <v-flex xs12>
        <h1>{{ $t('signup.title') }}</h1>
      </v-flex>
      <v-flex xs12>
        <span class="body-2"><nuxt-link :to="$i18n.path('login')">{{ $t('signup.already') }}</nuxt-link></span>
      </v-flex>
      <v-flex xs12>
        <div @click="auth('facebook')">
          <img width="200px" src="https://s3.eu-west-2.amazonaws.com/sc30/static/facebook-login.png" alt="Facebook Login"/>
        </div>
      </v-flex>
    </v-layout>

    <div v-if="socialLoginError">
      {{ socialLoginError }}
    </div>
  </v-container>
</template>

<style>

</style>

<script>
export default {
  transition: {
    name: 'page',
    mode: 'out-in'
  },
  methods: {
    async login () {
    },
    async auth (network) {
      const hello = this.hello
      try {
        const authRes = await hello(network).login()

        let profile = await hello(network).api('me')

        profile.login_source = network
        profile.token = authRes.authResponse.access_token

        const user = await this.$axios.post(`api/v1/user/register/${network}`, profile)
        console.log(user)
      } catch (e) {
        this.socialLoginError = e
      }
    }
  },
  data () {
    return {
      socialLoginError: false,
      loginError: false
    }
  }
}
</script>