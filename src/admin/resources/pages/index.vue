<template>
  <section>
    <v-container grid-list-md>
      <v-layout align-center justify-center row wrap>
        <v-flex xs12>
          <form>
            <v-text-field
              label="Login Email"
              v-model="email"
              :error-messages="emailErrors"
              @input="$v.email.$touch()"
              @blur="$v.email.$touch()"
              required
            ></v-text-field>
            <v-text-field
              label="Password"
              v-model="password"
              :error-messages="passwordErrors"
              @input="$v.password.$touch()"
              @blur="$v.password.$touch()"
              required
            ></v-text-field>
            test
            <v-btn @click="submit">Submit</v-btn>
            <v-btn @click="clear">Clear</v-btn>
          </form>
        </v-flex>
      </v-layout>
    </v-container>
  </section>
</template>

<script>
  import { validationMixin } from 'vuelidate'
  import { required, email } from 'vuelidate/lib/validators'

  export default {
    mixins: [validationMixin],
    validations: {
      email: { required, email },
      password: { required }
    },
    data () {
      return {
        email: '',
        password: '',
        formError: null
      }
    },
    methods: {
      async logout () {
        try {
          await this.$store.dispatch('logout')
        } catch (e) {
          this.formError = e.message
        }
      },
      async submit () {
        this.$v.$touch() // touch the form, this will validate or invalidate inputs

        if (!this.$v.$invalid) { // if false, the form is valid and we can try to login
          try {
            const data = await this.$store.dispatch('login', {
              email: this.email,
              password: this.password
            })
            this.$cookie.set('sc_auth', data.token, { expires: 7 }) // save as a token, this is for SSR
            this.$axios.defaults.headers.Authorization = 'Bearer ' + data.token
            this.email = ''
            this.password = ''
            this.formError = null
            this.$router.push({ path: '/home' })
          } catch (e) {
            this.formError = e.message
            console.log(e)
          }
        }
      },
      clear () {
        this.$v.$reset()
        this.name = ''
        this.email = ''
        this.formError = null
      }
    },
    computed: {
      passwordErrors () {
        const errors = []
        if (!this.$v.password.$dirty) return errors
        !this.$v.password.required && errors.push('Password is required.')
        return errors
      },
      emailErrors () {
        const errors = []
        if (!this.$v.email.$dirty) return errors
        !this.$v.email.email && errors.push('Must be valid e-mail')
        !this.$v.email.required && errors.push('E-mail is required')
        return errors
      }
    }
  }
</script>