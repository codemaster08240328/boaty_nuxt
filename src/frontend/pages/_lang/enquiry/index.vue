<template>
  <section>
    <v-parallax
      class="yacht-charter"
      title="Family & friends enjoying sunny day chartering a yacht" 
      alt="Family & friends enjoying sunny day chartering a yacht" 
      src="https://s3-eu-west-2.amazonaws.com/sc30/uploads/2016/03/20010836/sea-adventure.jpg" 
      height="260">
      <v-layout
        column
        align-center
        justify-center
        class="white--text"
      >
        <h1 class="white--text mb-2 display-2 text-xs-center">Are you looking to charter?</h1>
        <h2 style="width: 80%;" class="title text-xs-center">Sail with SailChecker</h2>
      </v-layout>
      <v-layout justify-center align-center class="parallax-psp hidden-sm-and-down">
        <div class="items">
          <h4 class="body-1">SailChecker in the press</h4>
          <img 
            style="max-width: 600px;" 
            src="https://s3.eu-west-2.amazonaws.com/sc30/static/awards/in-the-press-min.png" 
            alt="SailChecker's appearances in press"
          />
        </div>
      </v-layout>
    </v-parallax>

    <v-breadcrumbs id="breadcrumbs" justify-center>
      <v-icon slot="divider">forward</v-icon>
      <v-breadcrumbs-item :href="breadcrumb['@id']" v-for="breadcrumb in breadcrumbs" :key="breadcrumb['@id']">
        {{ breadcrumb.name }}
      </v-breadcrumbs-item>
    </v-breadcrumbs>

    <v-container class="mt-0 pt-0">
      <v-layout row wrap>
        <v-flex xs12 md8 class="pb-4">
          <h2 class="cyan--text text--darken-3">Yacht Charter Enquiry</h2>
          <p>In office hours, we typically respond in less than 1 hour.</p>
          <p>
            We get requests from individuals sailing for the first time, groups of friends looking to have a trip so memorable it will last a lifetime, to families, young and old, to corporates seeking highly organised regattas on the very best yachts. Our database is the largest of any yacht charter booking platform from the smallest sailboat, to a luxury superyacht.
          </p>
          <v-card>
            <v-container class="cyan darken-4 px-0 pb-0">
              <v-layout row wrap class="white--text text-xs-center mb-2" >
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
        </v-flex>
        <v-flex xs12 md4 class="px-4">
          <h2 class="cyan--text text--darken-3">Price Guarantee</h2>
          <p>
            All our customers enjoy our high level of customer service, whether budget or luxury is driving your check. We guarantee we will not be beaten on price.
          </p>
          <p>
            Our experienced team combined the latest search tech and a broad experience of the charter market to bring you a range of options or simply ideas.
          </p>

          <p class="text-xs-center" style="text-decoration: none;">
            <a href="https://uk.trustpilot.com/review/sailchecker.com?utm_medium=Trustbox&utm_source=EmailSignature2" target="_blank" style='text-decoration:none;text-underline: none;'>
            <img src="https://emailsignature.trustpilot.com/signature/en-GB/2/55d30fc10000ff00058247d7/text.png" border="0" height="16" style="max-height: 16px;" alt="Trustpilot rating"><br>
            <img src="https://emailsignature.trustpilot.com/signature/en-GB/2/55d30fc10000ff00058247d7/stars.png" border="0" width="160" style="max-width: 160px; width: 100%;" alt="Trustpilot Stars"></a>
            <br>
            <a href="https://uk.trustpilot.com/review/sailchecker.com?utm_medium=Trustbox&utm_source=EmailSignature2" target="_blank" style='text-decoration:none;text-underline: none;'><img src="https://emailsignature.trustpilot.com/brand/s/2/logo.png" border="0" width="79" style="max-width: 79px; width: 100%;" alt="Trustpilot Logo"></a>
          </p>
        </v-flex>
      </v-layout>
    </v-container>
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
import MetaService from '~/services/meta'
import Enquire from '~/components/Enquire'
import { validationMixin } from 'vuelidate'
import { required, maxLength, email, numeric } from 'vuelidate/lib/validators'
import phone from '~/assets/js/phone'

export default {
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
  components: {
    Enquire
  },
  transition: {
    name: 'page',
    mode: 'out-in'
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
  async asyncData ({ breadcrumbs, error }) {
    try {
      return {
        breadcrumbs: breadcrumbs
      }
    } catch (e) {
      error({ statusCode: 404, message: 'Page not found' })
    }
  },
  data () {
    return {
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
  },
  head () {
    const breadcrumbsJSON = MetaService.breadcrumbsJSON(this.breadcrumbs)

    const meta = MetaService.base({
      title: 'Charter Enquiry',
      description: 'Send us your charter enquiry today and we will make sure that we send the best available options within 24 hours.',
      image: 'https://s3.eu-west-2.amazonaws.com/sc30/uploads/2018/02/chris-1-1.png',
      creator: 'Chris Lait',
      type: 'website',
      url: this.$i18n.path(this.$route.path)
    })

    return {
      title: 'Charter Enquiry',
      meta: meta,
      link: [
        {
          hid: 'en',
          rel: 'alternate',
          href: this.baseUrl + this.$i18n.hreflang(this.$route.path),
          hreflang: 'x-default'
        }
        // {
        //   hid: 'engb',
        //   rel: 'alternate',
        //   href: this.baseUrl + `gb/${this.$i18n.hreflang(this.$route.path)}`,
        //   hreflang: 'en-gb'
        // }
      ],
      script: [
        { innerHTML: breadcrumbsJSON, type: 'application/ld+json' }
      ],
      __dangerouslyDisableSanitizers: ['script']
    }
  }
}
</script>