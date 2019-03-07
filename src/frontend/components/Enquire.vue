<template>
  <section>
    <v-dialog v-model="dialog" :fullscreen="$vuetify.breakpoint.xsOnly" transition="dialog-bottom-transition" persistent max-width="800px">
      <v-btn large :class="buttonClass || 'cyan darken-2'" class="white--text" slot="activator">{{ buttonTitle || 'Enquire'}}</v-btn>
      <v-card>
        <v-container class="cyan darken-4 px-0 pb-0">
          <v-layout row wrap class="hidden-xs-only white--text text-xs-center mb-2" >
            <v-flex xs4>
              <div>
                <v-icon color="green" class="display-1">money_off</v-icon>
              </div>
              <div class="hidden-sm-and-down subheading">{{ $t('searchPage.moneyofftitle') }}</div>
              <p class="hidden-sm-and-down">{{ $t('searchPage.moneyoff') }}</p>
            </v-flex>
            <v-flex xs4>
              <div>
                <v-icon color="blue" class="display-1">credit_card</v-icon>
              </div>
              <div class="hidden-sm-and-down subheading">{{ $t('searchPage.securetitle') }}</div>
              <p class="hidden-sm-and-down">{{ $t('searchPage.secure') }}</p>
            </v-flex>
            <v-flex xs4>
              <div>
                <v-icon color="orange" class="display-1">event_available</v-icon>
              </div>
              <div class="hidden-sm-and-down subheading">{{ $t('searchPage.availabletitle') }}</div>
              <p class="hidden-sm-and-down">{{ $t('searchPage.available') }}</p>
            </v-flex>
          </v-layout>
          <v-layout row wrap class="pa-2 white--text text-sm-center body-1" align-center justify-center v-if="boat">
            <v-flex xs12 sm6>
              Vessel Name: <b>{{  boat.Name }}</b>
              <div v-if="$store.state.search.date">
                Date: <b>{{ $store.state.search.date }}</b>
              </div>
              Base: <b>{{ boat.base_name }}</b>
            </v-flex>
            <v-flex xs12 sm6>
              <div class="hidden-sm-and-down">
                Model: <b>{{ boat.ModelName }}</b>
              </div>
              Price: <price class="hidden-xs-and-down headline" :currency="'EUR'" :value="boat.boatPriceWithDiscount"></price>
            </v-flex>
          </v-layout>
          <v-layout row wrap>
            <v-flex xs12>
              <v-stepper v-model="e1">
                <v-stepper-header>
                  <v-stepper-step step="1" :complete="e1 > 1">{{ $t('enquire.step1') }}</v-stepper-step>
                  <v-divider></v-divider>
                  <v-stepper-step step="2" :complete="e1 > 2">{{ $t('enquire.step2') }}</v-stepper-step>
                  <v-divider></v-divider>
                  <v-stepper-step step="3">{{ $t('enquire.step3') }}</v-stepper-step>
                </v-stepper-header>
                <v-stepper-items>
                  <v-stepper-content step="1">
                    <v-container class="pa-0 ma-0">
                      <v-layout row wrap>
                        <v-flex xs12>
                          <v-text-field
                            light
                            v-model="form.step1.name"
                            :label="$t('MultiBoatCard.name')" 
                            :hint="$t('MultiBoatCard.namehint')" 
                            persistent-hint 
                            required
                            :value="'Matthew Gould'"
                            :error-messages="nameErrors"
                            @input="$v.form.step1.name.$touch()"
                            @blur="$v.form.step1.name.$touch()"
                            ></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                          <v-text-field
                            light
                            v-model="form.step1.email" 
                            :label="$t('MultiBoatCard.email')"
                            :hint="$t('MultiBoatCard.emailhint')"
                            persistent-hint
                            required
                            :error-messages="emailErrors"
                            @input="$v.form.step1.email.$touch()"
                            @blur="$v.form.step1.email.$touch()"
                            ></v-text-field>
                        </v-flex>
                      </v-layout>
                    </v-container>
                    <v-btn color="primary" @click="nextStep(1)">{{ $t('utility.continue') }}</v-btn>
                    <v-btn flat @click="close()">{{ $t('utility.close') }}</v-btn>
                    <v-progress-linear v-show="created" :indeterminate="true"></v-progress-linear>
                  </v-stepper-content>
                  <v-stepper-content step="2">
                    <v-container class="pa-0 ma-0">
                      <v-layout row wrap>
                        <v-flex xs12>
                          <v-select
                            :items="codes"
                            v-model="phoneCode"
                            label="Select"
                            single-line
                            dense
                            prepend-icon="phone"
                          ></v-select>
                          <v-text-field 
                            mask="####-###-########"
                            v-model="form.step2.phone_number" 
                            :label="$t('MultiBoatCard.mobilenumber')" 
                            :hint="$t('MultiBoatCard.mobilenumberhint')"
                            persistent-hint
                            required
                            :error-messages="phone_numberErrors"
                            @input="$v.form.step2.phone_number.$touch()"
                            @blur="$v.form.step2.phone_number.$touch()"
                            ></v-text-field>
                        </v-flex>
                      </v-layout>
                    </v-container>
                    <v-btn color="primary" @click="nextStep(2)">{{ $t('utility.continue') }}</v-btn>
                    <v-btn flat @click="close()">{{ $t('utility.close') }}</v-btn>
                  </v-stepper-content>
                  <v-stepper-content step="3">
                    <v-container class="pa-0 ma-0">
                      <v-layout row wrap>
                        <v-flex xs12 sm6>
                          <v-text-field 
                            min="0" 
                            max="20"
                            type="number"
                            mask="##" 
                            v-model="form.step3.adults" 
                            :label="$t('MultiBoatCard.adults')"
                            :hint="$t('MultiBoatCard.adultshint')"
                            persistent-hint
                            :error-messages="adultsErrors"
                            @input="$v.form.step3.adults.$touch()"
                            @blur="$v.form.step3.adults.$touch()"
                            ></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6>
                          <v-text-field 
                            min="0" a
                            max="20" 
                            mask="##"
                            type="number"
                            v-model="form.step3.children" 
                            :label="$t('MultiBoatCard.children')"
                            :hint="$t('MultiBoatCard.childrenhint')"
                            persistent-hint
                            :error-messages="childrenErrors"
                            @input="$v.form.step3.children.$touch()"
                            @blur="$v.form.step3.children.$touch()"
                            ></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                          <v-text-field 
                            multi-line 
                            v-model="form.step3.message" 
                            :label="$t('MultiBoatCard.message')"
                            :hint="$t('MultiBoatCard.messagehint')"
                            persistent-hint
                            :error-messages="messageErrors"
                            @input="$v.form.step3.message.$touch()"
                            @blur="$v.form.step3.message.$touch()"
                            ></v-text-field>
                        </v-flex>
                      </v-layout>
                    </v-container>
                    <v-btn color="primary" @click.native="nextStep(3)">{{ $t('utility.save') }}</v-btn>
                    <v-btn flat @click="close()">{{ $t('utility.close') }}</v-btn>
                  </v-stepper-content>
                </v-stepper-items>
              </v-stepper>
            </v-flex>
          </v-layout>
        </v-container>
      </v-card>
    </v-dialog>
    <v-snackbar
      :timeout="6000"
      v-model="contactSnack"
      vertical
      bottom
      color="cyan darken-4"
    >
      Thank you {{ $store.state.user.name }}, we have received you message and will get back to you shortly.
      <v-btn flat dark @click.native="contactSnack = false">Close</v-btn>
    </v-snackbar>
  </section>
</template>

<script>
import { validationMixin } from 'vuelidate'
import { required, maxLength, email, numeric } from 'vuelidate/lib/validators'
import Price from '~/components/Price'
import phone from '~/assets/js/phone'

export default {
  components: {
    Price
  },
  mixins: [validationMixin],
  validations: {
    form: {
      step1: {
        name: { required, maxLength: maxLength(100) },
        email: { required, email }
      },
      step2: {
        phone_number: { required, maxLength: maxLength(50) }
      },
      step3: {
        message: { maxLength: maxLength(500) },
        adults: { numeric },
        children: { numeric }
      }
    }
  },
  props: [
    'boat',
    'buttonTitle',
    'buttonClass'
  ],
  computed: {
    nameErrors () {
      const errors = []
      if (!this.$v.form.step1.name.$dirty) return errors
      !this.$v.form.step1.name.required && errors.push('Name is required.')
      !this.$v.form.step1.name.maxLength && errors.push('Name must be at most 100 characters.')
      return errors
    },
    emailErrors () {
      const errors = []
      if (!this.$v.form.step1.email.$dirty) return errors
      !this.$v.form.step1.email.required && errors.push('Email is required.')
      !this.$v.form.step1.email.email && errors.push('Email is not valid.')
      return errors
    },
    messageErrors () {
      const errors = []
      if (!this.$v.form.step3.message.$dirty) return errors
      !this.$v.form.step3.message.maxLength && errors.push('Messages should be less than 500 characters.')
      return errors
    },
    phone_numberErrors () {
      const errors = []
      if (!this.$v.form.step2.phone_number.$dirty) return errors
      !this.$v.form.step2.phone_number.required && errors.push('A Phone Number is required.')
      !this.$v.form.step2.phone_number.maxLength && errors.push('Phone numbers should be less than 50 characters!')
      return errors
    },
    adultsErrors () {
      const errors = []
      if (!this.$v.form.step3.adults.$dirty) return errors
      !this.$v.form.step3.adults.numeric && errors.push('This field has to be numeric.')
      return errors
    },
    childrenErrors () {
      const errors = []
      if (!this.$v.form.step3.children.$dirty) return errors
      !this.$v.form.step3.children.numeric && errors.push('This field has to be numeric.')
      return errors
    }
  },
  methods: {
    close () {
      this.dialog = false
      this.form.contact_id = false
      this.form.confirm_code = ''
      this.e1 = 1

      this.$v.$reset()

      this.form.step1.name = this.$store.state.user.name || ''
      this.form.step1.email = this.$store.state.user.email || ''
      this.form.step2.phone_number = ''
      this.form.step3.adults = 1
      this.form.step3.children = 1
      this.form.step3.message = ''
    },
    async nextStep (step) {
      const formStep = `step${step}`
      this.$v.form[formStep].$touch()

      if (!this.$v.form[formStep].$invalid) {
        let contact = {}

        if (step >= 1) {
          contact.name = this.form.step1.name
          contact.email = this.form.step1.email

          const details = {
            name: this.form.step1.name,
            email: this.form.step1.email
          }

          this.$store.commit('setContactUser', details)
        }

        if (step >= 2) {
          const code = (this.phoneCode) ? '+' + this.phoneCode + ' - ' : ''

          contact.phone_number = code + this.form.step2.phone_number
          this.$store.commit('setContactUser', { phone_number: contact.phone_number })
        }

        if (step === 3) {
          contact.children = this.form.step3.children
          contact.adults = this.form.step3.adults
          contact.message = this.form.step3.message
          this.$store.commit('setContactUser', {
            meta: {
              contact: {
                adults: contact.adults,
                children: contact.children
              }
            }
          })
        }

        try {
          if (!this.form.contact_id) {
            this.created = true
            console.log('saving', contact)

            if (this.boat) {
              contact.boat_id = this.boat.ID
              contact.base_id = this.boat.base_id
              contact.currency = this.$store.state.defaultCurrency
              contact.exchange_rate = this.$store.state.fx.rates[this.$store.state.defaultCurrency]
              contact.boat_price_euro = this.boat.boatPriceWithDiscount
              contact.date = (this.$store.state.search.date) ? this.$store.state.search.date : false
              contact.weeks = 1 // make dynamic
            }

            if (this.$cookie.get('gclid') !== null) {
              contact.gclid = this.$cookie.get('gclid')
            }
            const { data } = await this.$axios.post('/api/v1/contact/store', contact)
            console.log('data', data)
            this.form.contact_id = data.id
            this.form.confirm_code = data.confirm_code
            this.created = false

            if (window.dataLayer) {
              window.dataLayer.push({'event': 'EnquiryFormSubmitted'})
            }
          } else {
            console.log('updating', contact)
            console.log('confirm_code', this.form.confirm_code)
            const { data } = await this.$axios.post(`/api/v1/contact/update/${this.form.confirm_code}`, contact)
            console.log(data)
          }
        } catch (e) {
          throw new Error(e)
        }

        this.e1 = step + 1 // steps start at 2
        if (this.e1 === 4) {
          this.dialog = false
          this.form.contact_id = false
          this.form.confirm_code = ''
          this.e1 = 1

          this.$v.$reset()

          this.form.step1.name = this.$store.state.user.name || ''
          this.form.step1.email = this.$store.state.user.email || ''
          this.form.step2.phone_number = ''
          this.form.step3.adults = 1
          this.form.step3.children = 1
          this.form.step3.message = ''

          this.contactSnack = true
        }
      }
    }
  },
  data () {
    return {
      dialog: false,
      contactSnack: false,
      created: false,
      phoneCode: (this.$store.state.locale === 'en-us') ? '1' : '44',
      codes: phone.codes,
      e1: 0,
      form: {
        step1: {
          name: this.$store.state.user.name || '',
          email: this.$store.state.user.email || ''
        },
        step2: {
          phone_number: ''
        },
        step3: {
          adults: 1,
          children: 1,
          message: ''
        },
        contact_id: false,
        confirm_code: ''
      }
    }
  }
}
</script>