<template>
  <section>
    <v-card>
      <v-card-title class="cyan darken-3 white--text">
        <h2 class="title mb-0">{{ $t('review.title') }}</h2>
      </v-card-title>
      <v-card-text>
        <v-container fluid>
          <v-layout row wrap>
            <v-flex xs12 md8>
              <!-- hydration error, fix this -->
              <div v-for="review in reviews" v-bind:key="review.id">
                <v-card class="mb-2">
                  <v-card-title class="cyan darken-3 white--text">
                    <h3 class="title mb-0 mr-4">{{ review.name }}</h3>
                    <no-ssr>
                      <star-rating 
                        v-bind:increment="1"
                        :read-only="true"
                        :rating="review.rating"
                        v-bind:max-rating="5"
                        inactive-color="#afdbd6"
                        :show-rating="false"
                        v-bind:star-size="20">
                      </star-rating>
                    </no-ssr>
                  </v-card-title>
                  <v-card-text>
                    <span class="body-2">{{ review.message }}</span>
                  </v-card-text>
                  <v-card-actions>
                    <span class="body-1">{{ $t('review.published') }} {{ review.created_at }}</span>
                  </v-card-actions>
                </v-card>
              </div>
              <div>
                <span class="body-2">{{ $t('review.noreviews') }}</span>
              </div>
            </v-flex>
            <v-flex xs12 md4>
              <v-btn class="cyan darken-3 white--text" block large @click.native="reviewDialog = true">
                {{ $t('review.write-a-review') }}<v-icon right>rate_review</v-icon>
              </v-btn>
            </v-flex>
          </v-layout>
        </v-container>
      </v-card-text>
    </v-card>

    <v-dialog v-model="reviewDialog" persistent max-width="650">
      <v-card>
        <v-toolbar class="cyan darken-3 white--text headline">
          <v-btn icon @click.native="reviewDialog = false" dark>
            <v-icon>close</v-icon>
          </v-btn>
          <v-toolbar-title>{{ $t('review.dialog.title') }}</v-toolbar-title>
        </v-toolbar>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout row wrap>
              <v-flex xs12>
                <span class="body-2">{{ $t('review.rating') }}</span>
                <no-ssr>
                <star-rating v-bind:increment="1" 
                  v-bind:max-rating="5"
                  v-model="review.rating"
                  inactive-color="#afdbd6"
                  @input="$v.review.rating.$touch()"
                  @blur="$v.review.rating.$touch()"                  
                  v-bind:star-size="30">
                </star-rating>
                </no-ssr>
                <div class="input-group--error input-group--text-field error--text" v-show="reviewRatingErrors.length > 0">
                  <span v-for="error in reviewRatingErrors" v-bind:key="error">
                    {{ error }}
                  </span>
                </div>         
              </v-flex>
              <v-flex xs12>
                <v-text-field 
                  v-model="review.name" 
                  label="Your Name"
                  :error-messages="reviewNameErrors"
                  @input="$v.review.name.$touch()"
                  @blur="$v.review.name.$touch()"
                  ></v-text-field>
              </v-flex>
              <v-flex xs12>
                <v-text-field 
                  v-model="review.email" 
                  label="Your Email"
                  :error-messages="reviewEmailErrors"
                  @input="$v.review.email.$touch()"
                  @blur="$v.review.email.$touch()"
                ></v-text-field>
              </v-flex>
              <v-flex xs12>
                <v-text-field 
                  mask="####-###-########"
                  v-model="review.phone_number" 
                  label="Your Phone Number"
                  :error-messages="reviewPhone_numberErrors"
                  @input="$v.review.phone_number.$touch()"
                  @blur="$v.review.phone_number.$touch()"
                ></v-text-field>
              </v-flex>
              <v-flex xs12>
                <v-text-field 
                  multi-line
                  v-model="review.message" 
                  label="Your Review"
                  :error-messages="reviewMessageErrors"
                  @input="$v.review.message.$touch()"
                  @blur="$v.review.message.$touch()"
                ></v-text-field>
              </v-flex>
            </v-layout>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="cyan" flat light @click.native="reviewDialog = false">{{ $t('utility.cancel') }}</v-btn>
          <v-btn color="cyan darken-1" block large dark @click="sendReview()">
            {{ $t('utility.send') }}
            <v-progress-circular v-show="sendingReview" class="ml-4" indeterminate v-bind:size="30" v-bind:width="7" color="orange "></v-progress-circular>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-snackbar
      :timeout="8000"
      v-model="snackbar"
      vertical
      bottom
    >
      {{ $t('review.snackbar', { name: $store.state.user.name })}}
      <v-btn flat color="cyan" @click.native="snackbar = false">{{ $t('utility.close') }}</v-btn>
    </v-snackbar>
  </section>
</template>

<script>
import { validationMixin } from 'vuelidate'
import { required, maxLength, email, numeric } from 'vuelidate/lib/validators'

export default {
  mixins: [validationMixin],
  validations: {
    review: {
      name: { required, maxLength: maxLength(100) },
      email: { required, email },
      message: { required, maxLength: maxLength(500) },
      phone_number: { required, maxLength: maxLength(50) },
      rating: { required, numeric }
    }
  },
  computed: {
    reviewNameErrors () {
      const errors = []
      if (!this.$v.review.name.$dirty) return errors
      !this.$v.review.name.required && errors.push('Name is required.')
      !this.$v.review.name.maxLength && errors.push('Name must be at most 100 characters.')
      return errors
    },
    reviewEmailErrors () {
      const errors = []
      if (!this.$v.review.email.$dirty) return errors
      !this.$v.review.email.required && errors.push('Email is required.')
      !this.$v.review.email.email && errors.push('Email is not valid.')
      return errors
    },
    reviewMessageErrors () {
      const errors = []
      if (!this.$v.review.message.$dirty) return errors
      !this.$v.review.message.required && errors.push('A review is required.')
      !this.$v.review.message.maxLength && errors.push('Messages should be less than 500 characters.')
      return errors
    },
    reviewPhone_numberErrors () {
      const errors = []
      if (!this.$v.review.phone_number.$dirty) return errors
      !this.$v.review.phone_number.required && errors.push('A Phone Number is required.')
      !this.$v.review.phone_number.maxLength && errors.push('Phone numbers should be less than 50 characters!')
      return errors
    },
    reviewRatingErrors () {
      const errors = []
      if (!this.$v.review.rating.$dirty) return errors
      !this.$v.review.rating.required && errors.push('Rating is required.')
      !this.$v.review.rating.numeric && errors.push('Rating must be numeric!')
      return errors
    }
  },
  data () {
    return {
      review: {
        name: '',
        email: '',
        phone_number: '',
        message: '',
        rating: null
      },
      reviewDialog: false,
      sendingReview: false,
      snackbar: false
    }
  },
  props: [
    'reviews',
    'boat_id'
  ],
  methods: {
    async sendReview () {
      this.$v.review.$touch()
      if (!this.$v.review.$invalid) {
        console.log('sending review')
        this.sendingReview = true
        let review = this.review
        review.BoatID = this.boat_id

        setTimeout(async () => {
          console.log(review)
          const { data } = await this.$axios.post('/api/v1/review', review)
          if (data.id > 0) {
            const details = {
              name: this.review.name,
              email: this.review.email,
              phone_number: this.review.phone_number
            }

            this.$store.commit('setContactUser', details)
          }

          /*
            reset form
          */
          this.$v.$reset()
          this.review.name = ''
          this.review.phone_number = ''
          this.review.email = ''
          this.review.message = ''
          this.review.rating = null

          this.reviewDialog = false
          this.sendingReview = false

          this.snackbar = true

          window.dataLayer.push({'event': 'ReviewFormSubmitted'})
        }, 1000)
      }
    }
  }
}
</script>
