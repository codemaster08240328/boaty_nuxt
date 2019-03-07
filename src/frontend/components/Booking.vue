<template>
  <section>
    <v-card v-show="!isMobile" class="booking-form" id="booking-form" v-scroll="onScroll">
      <!-- sailchecker price -->
      <v-card-title class="cyan darken-3 white--text">
        <v-container fluid>
          <v-layout align-center>
            <v-flex xs8>
              <p class="title"><v-icon color="white">label_outline</v-icon> SailChecker Price</p>
            </v-flex>
            <v-flex xs4>
              <price class="title" currency="EUR" :value="booking.price"></price>
            </v-flex>
          </v-layout>
        </v-container>
      </v-card-title>

      <!-- defined discount -->
      <v-card-title v-if="booking.price < booking.basePrice">
        <v-container fluid>
          <v-layout align-center>
            <v-flex xs8>
              <p class="title"><v-icon color="black">label</v-icon> {{ $t('boatPage.charterprices') }}</p>
            </v-flex>
            <v-flex xs4>
              <v-badge left color="red">
                <span slot="badge">-{{ booking.discount }}%</span>
                <price class="headline discount" currency="EUR" :value="booking.basePrice"></price>
              </v-badge>
            </v-flex>
          </v-layout>
        </v-container>
      </v-card-title>

      <!-- automatic 5% discount -->
      <v-card-title v-else-if="booking.price != 0" >
        <v-container fluid>
          <v-layout align-center>
            <v-flex xs8>
              <p class="title"><v-icon color="black">label</v-icon> {{ $t('boatPage.charterprices') }}</p>
            </v-flex>
            <v-flex xs4>
              <v-badge left color="red">
                <span slot="badge">-5%</span>
                <price class="headline discount" currency="EUR" :value="booking.basePrice * 1.05"></price>
              </v-badge>
            </v-flex>
          </v-layout>
        </v-container>
      </v-card-title>

      <!-- boat name -->
      <v-card-title style="padding-top: 0px;">
        <h1 class="display-1 mb-1">{{ page.boat.ModelName }}</h1>
      </v-card-title>

      <v-card-text style="padding-top: 0px;">
        <v-container fluid>
          <v-layout row wrap>
            <!-- time (if its available) -->
            <v-flex xs12>
              <v-dialog
                ref="menu"
                persistent
                v-model="modal"
                lazy
                full-width
                max-width="290px"
              >
                <v-text-field
                  slot="activator"
                  :label="$t('boatPage.yourdate')"
                  v-model="booking.date"
                  prepend-icon="event"
                  readonly
                  light
                ></v-text-field>
                <v-date-picker
                  ref="picker"
                  @change="save"
                  @input="booking.date = formatDate($event)" 
                  v-model="booking.date" 
                  :min="new Date().toISOString().substr(0, 10)"
                  max="2021-01-01">
                  <!-- TODO: :allowed-dates="allowedDates" -->
                </v-date-picker>
              </v-dialog>
            </v-flex>
            <!-- charter duration -->
            <v-flex xs12>
              <v-select
                :label="$t('boatPage.charterduration')"
                prepend-icon="access_time"
                :items="duration"
                v-model="booking.weeks"
                @input="booking.weeks = changeDuration($event)"
              >
              </v-select>
            </v-flex>
          </v-layout>
        </v-container>
      </v-card-text>

      <!-- get an offer or quote button -->
      <v-card-text >
        <div style="width: 100%" v-show="booking.date">
          <v-btn @click="reserveBoat()" large block class="orange darken-4 white--text">Reserve</v-btn>
        </div>
        <div v-show="!booking.date">
          <p class="title">Please Select A Date.</p>
        </div>

      </v-card-text>

      <v-card-text>
        <div class="subheading orange--text">This yacht is on people's minds!</div>
        <span class="body-2">This Yacht has been viewed more than 500 times in the last week.</span>
      </v-card-text>
    </v-card>

    <v-container v-if="isMobile" class="cyan darken-3 white--text pa-1 ma-0" id="mobile-booking-bottom-nav" fluid>
      <v-layout row wrap justify-center align-center>
        <v-flex xs4>
          <price class="title ml-3" currency="EUR" :value="booking.price"></price>
        </v-flex>
        <v-flex xs8>
          <v-btn @click="mobileReserveBoat()" large class="orange pull-right darken-4 white--text">Reserve</v-btn>
        </v-flex>
      </v-layout>
    </v-container>

   <v-dialog v-if="isMobile" v-model="mobileBooking" fullscreen :transition="'dialog-bottom-transition'" :overlay="false">
      <v-card>
        <v-toolbar dark color="primary">
          <v-btn icon @click.native="mobileBooking = false" dark>
            <v-icon>close</v-icon>
          </v-btn>
          <v-toolbar-title>Booking Details</v-toolbar-title>
        </v-toolbar>

        <v-card-title class="cyan darken-3 white--text">
          <v-container class="pa-1" fluid>
            <v-layout justify-center align-center>
              <v-flex xs8 class="title">
                <v-icon color="white">label_outline</v-icon> SailChecker Price
              </v-flex>
              <v-flex xs4>
                <price class="title" currency="EUR" :value="booking.price"></price>
              </v-flex>
            </v-layout>
          </v-container>
        </v-card-title>

        <!-- defined discount -->
        <v-card-title v-if="booking.price < booking.basePrice">
          <v-container class="pa-1" fluid>
            <v-layout align-center>
              <v-flex xs8 class="title">
                <v-icon color="black">label</v-icon> Charter Price
              </v-flex>
              <v-flex xs4>
                <v-badge left color="red">
                  <span slot="badge">-{{ booking.discount }}%</span>
                  <price class="headline discount" currency="EUR" :value="booking.basePrice"></price>
                </v-badge>
              </v-flex>
            </v-layout>
          </v-container>
        </v-card-title>

        <!-- automatic 5% discount -->
        <v-card-title v-else-if="booking.price != 0" >
          <v-container fluid>
            <v-layout align-center>
              <v-flex xs8 class="title">
                <v-icon color="black">label</v-icon> Charter Price
              </v-flex>
              <v-flex xs4>
                <v-badge left color="red">
                  <span slot="badge">-5%</span>
                  <price class="headline discount" currency="EUR" :value="booking.basePrice * 1.05"></price>
                </v-badge>
              </v-flex>
            </v-layout>
          </v-container>
        </v-card-title>

        <!-- boat name -->
        <v-card-title style="padding-top: 0px;">
          <h1 class="display-1 mb-1">{{ page.boat.ModelName }}</h1>
        </v-card-title>
  
        <v-card-text style="padding-top: 0px;">
          <v-container fluid>
            <v-layout row wrap>
              <!-- time (if its available) -->
              <v-flex xs12>
                <v-dialog
                  ref="menu"
                  persistent
                  v-model="modal"
                  lazy
                  full-width
                  max-width="290px"
                >
                  <v-text-field
                    slot="activator"
                    :label="$t('boatPage.yourdate')"
                    v-model="booking.date"
                    prepend-icon="event"
                    readonly
                    light
                  ></v-text-field>
                  <v-date-picker 
                    ref="picker"
                    @change="save"
                    @input="booking.date = formatDate($event)" 
                    v-model="booking.date" 
                    :min="new Date().toISOString().substr(0, 10)"
                    max="2021-01-01"
                  >
                  <!-- :allowed-dates="state.search" -->
                  </v-date-picker>
                </v-dialog>
              </v-flex>
              <!-- charter duration -->
              <v-flex xs12>
                <v-select
                  :label="$t('boatPage.charterduration')"
                  prepend-icon="access_time"
                  :items="duration"
                  v-model="booking.weeks"
                  @input="booking.weeks = changeDuration($event)"
                >
                </v-select>
              </v-flex>
            </v-layout>
          </v-container>
        </v-card-text>

        <!-- get anbeen looked at over offer or quote button -->
        <v-card-text >
          <div style="width: 100%" v-show="booking.date">
            <v-btn @click="reserveBoat()" large block class="orange darken-4 white--text">Reserve</v-btn>
          </div>
          <div v-show="!booking.date">
            <p class="title">Please Select A Date.</p>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialog" persistent max-width="650">
      <v-card>
        <v-toolbar class="cyan darken-3 white--text headline">
          <v-btn icon @click.native="dialog = false" dark>
            <v-icon>close</v-icon>
          </v-btn>
          <v-toolbar-title>{{ $t('booking.dialog.title') }}</v-toolbar-title>
        </v-toolbar>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
              <v-flex xs12 sm6>
                {{ $t('booking.dialog.vesselname') }}: <b>{{ dialogInfo.name }}</b><br/>
                {{ $t('booking.dialog.model') }}: <b>{{ dialogInfo.model }}</b><br/>
                {{ $t('booking.dialog.base') }} : <b>{{ dialogInfo.base }}</b>
              </v-flex>
              <v-flex xs12 sm6>
                <div v-if="booking.date">
                  {{ $t('booking.dialog.date') }}: <b>{{ booking.date }}</b>
                </div>
                {{ $t('booking.dialog.duration') }}: <b>{{ booking.weeks }} <span v-if="booking.weeks == 1">week</span><span v-else>weeks</span></b><br/>
                {{ $t('booking.dialog.price') }}: <price :currency="dialogInfo.currency" :value="dialogInfo.price"></price>
              </v-flex>
              <v-flex xs12 sm6>
                <v-text-field 
                  min="0" 
                  max="20" 
                  mask="##" 
                  v-model="form.adults" 
                  :label="$t('MultiBoatCard.adults')"
                  :error-messages="adultsErrors"
                  @input="$v.form.adults.$touch()"
                  @blur="$v.form.adults.$touch()"
                  ></v-text-field>
              </v-flex>
              <v-flex xs12 sm6>
                <v-text-field 
                  min="0" 
                  max="20" 
                  mask="##" 
                  v-model="form.children" 
                  :label="$t('MultiBoatCard.children')"
                  :error-messages="childrenErrors"
                  @input="$v.form.children.$touch()"
                  @blur="$v.form.children.$touch()"
                  ></v-text-field>
              </v-flex>
              <v-flex xs12>
                <v-text-field 
                  v-model="form.name" 
                  :label="$t('MultiBoatCard.name')" 
                  :hint="$t('MultiBoatCard.namehint')" 
                  persistent-hint 
                  required
                  :error-messages="nameErrors"
                  @input="$v.form.name.$touch()"
                  @blur="$v.form.name.$touch()"
                  ></v-text-field>
              </v-flex>
              <v-flex xs12>
                <v-text-field 
                  v-model="form.email" 
                  :label="$t('MultiBoatCard.email')" 
                  required
                  :error-messages="emailErrors"
                  @input="$v.form.email.$touch()"
                  @blur="$v.form.email.$touch()"
                  ></v-text-field>
              </v-flex>
              <v-flex xs12>
                <v-text-field 
                  mask="####-###-########"
                  v-model="form.phone_number" 
                  :label="$t('MultiBoatCard.mobilenumber')" 
                  required
                  :error-messages="phone_numberErrors"
                  @input="$v.form.phone_number.$touch()"
                  @blur="$v.form.phone_number.$touch()"
                  ></v-text-field>
              </v-flex>
              <v-flex xs12>
                <v-text-field 
                  multi-line 
                  v-model="form.message" 
                  :label="$t('MultiBoatCard.message')"
                  :error-messages="messageErrors"
                  @input="$v.form.message.$touch()"
                  @blur="$v.form.message.$touch()"
                  ></v-text-field>
              </v-flex>
            </v-layout>
          </v-container>          
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="cyan" flat light @click.native="dialog = false">{{ $t('MultiBoatCard.cancel') }}</v-btn>
          <v-btn color="cyan darken-1" block large dark @click="sendForm()">
            {{ $t('MultiBoatCard.send') }}
            <v-progress-circular v-show="sendingForm" class="ml-4" indeterminate v-bind:size="30" v-bind:width="7" color="orange "></v-progress-circular>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-snackbar
      :timeout="8000"
      v-model="contactSnack"
      vertical
    >
      {{ $t('booking.snackbar', { name: $store.state.user.name }) }}
      <v-btn flat color="cyan" @click.native="contactSnack = false">{{ $t('utility.close') }}</v-btn>
    </v-snackbar>


  </section>
</template>

<style scoped>
#mobile-booking-bottom-nav {
  position: fixed;
  bottom: 0px;
  left: 0px;
  z-index: 10;
}
.boat-page {
  position: relative;
}
.booking-form {
  position: absolute;
  max-width: 400px;
  transition: transform 900ms ease;
}
.booking-form .card__actions {
  bottom: 0px;
  position: absolute;
  width: 100%;
}
.booking-form .container.fluid {
  padding: 0px;
}
.booking-form .container.fluid .headline, .booking-form .container.fluid .title {
  margin-bottom: 0px;
}
.booking-form .badge span {
background: red;
    border-radius: 50%;
    padding: 3px;

}
</style>

<script>
import Price from '~/components/Price'
import { validationMixin } from 'vuelidate'
import { required, maxLength, email, numeric } from 'vuelidate/lib/validators'
import { each } from 'lodash'

export default {
  components: {
    Price
  },
  mixins: [validationMixin],
  validations: {
    form: {
      name: { required, maxLength: maxLength(100) },
      email: { required, email },
      message: { maxLength: maxLength(500) },
      phone_number: { required, maxLength: maxLength(50) },
      adults: { numeric },
      children: { numeric }
    }
  },
  computed: {
    nameErrors () {
      const errors = []
      if (!this.$v.form.name.$dirty) return errors
      !this.$v.form.name.required && errors.push('Name is required.')
      !this.$v.form.name.maxLength && errors.push('Name must be at most 100 characters.')
      return errors
    },
    emailErrors () {
      const errors = []
      if (!this.$v.form.email.$dirty) return errors
      !this.$v.form.email.required && errors.push('Email is required.')
      !this.$v.form.email.email && errors.push('Email is not valid.')
      return errors
    },
    messageErrors () {
      const errors = []
      if (!this.$v.form.message.$dirty) return errors
      !this.$v.form.message.maxLength && errors.push('Messages should be less than 500 characters.')
      return errors
    },
    phone_numberErrors () {
      const errors = []
      if (!this.$v.form.phone_number.$dirty) return errors
      !this.$v.form.phone_number.required && errors.push('A Phone Number is required.')
      !this.$v.form.phone_number.maxLength && errors.push('Phone numbers should be less than 50 characters!')
      return errors
    },
    adultsErrors () {
      const errors = []
      if (!this.$v.form.adults.$dirty) return errors
      !this.$v.form.adults.numeric && errors.push('This field has to be numeric.')
      return errors
    },
    childrenErrors () {
      const errors = []
      if (!this.$v.form.children.$dirty) return errors
      !this.$v.form.children.numeric && errors.push('This field has to be numeric.')
      return errors
    }
  },
  methods: {
    save (date) {
      this.$store.commit('setSearch', { date: date })
      this.$interaction.setDataInCookies({ name: 'search', data: { date: date } })
      this.$refs.menu.save(date)
    },
    allowedDates: val => {
      const d = new Date(val)
      if (d.getDay() === 6) {
        return 1
      } else {
        return 0
      }
    },
    onScroll (e) {
      if (this.isMobile) {
        return false
      }

      this.offsetTop = window.pageYOffset || document.documentElement.scrollTop

      let BoatInfo = document.getElementById('boat-info')
      let Booking = document.getElementById('booking-form')
      let Breadcrumbs = document.getElementById('breadcrumbs').getBoundingClientRect()
      let BoatInfoRect = BoatInfo.getBoundingClientRect()

      // detects top and resets
      if (Breadcrumbs.bottom < this.offsetTop && BoatInfoRect.bottom > Booking.offsetHeight) {
        Booking.style.position = 'fixed'
        Booking.style.top = '80px'
      } else {
        Booking.style.position = 'absolute'
        Booking.style.top = null
      }

      // sticks to bottom of boat info
      if (BoatInfoRect.bottom - 80 < Booking.offsetHeight) {
        Booking.style.position = 'absolute'
        const height = BoatInfo.scrollTop + BoatInfo.clientHeight - (Booking.offsetHeight / 2) - 130
        Booking.style.top = height + 'px'
      }
    },
    mobileReserveBoat () {
      console.log('popup for reserve')
      this.mobileBooking = true
    },
    reserveBoat () {
      console.log('reserve boat')
      // show the dialog
      this.dialog = true
      this.dialoagBoat = this.page.boat

      this.dialogInfo.name = this.page.boat.Name
      this.dialogInfo.model = this.page.boat.ModelName
      this.dialogInfo.base = this.page.boat.location.base_name
      this.dialogInfo.currency = 'EUR'
      this.dialogInfo.price = this.booking.price

      this.dialogInfo.duration = this.booking.weeks
      this.dialogInfo.embarkation_date = this.booking.date
    },
    async sendForm () {
      this.$v.form.$touch() // make sure form is dirty
      if (!this.$v.form.$invalid) {
        console.log('sending reserve')
        this.sendingForm = true
        let booking = this.form

        booking.boat_id = this.page.boat.ID
        booking.base_id = this.page.boat.location.base_id
        booking.currency = this.$store.state.defaultCurrency
        booking.exchange_rate = this.$store.state.fx.rates[this.$store.state.defaultCurrency]
        booking.date = this.booking.date
        booking.weeks = this.booking.weeks
        if (this.$cookie.get('gclid') !== null) {
          booking.gclid = this.$cookie.get('gclid')
        }
        booking.boat_price_euro = this.booking.price

        try {
          setTimeout(async () => {
            const { data } = await this.$axios.post('/api/v1/booking/store', booking)
            if (data.id > 0) {
              const details = {
                name: this.form.name,
                email: this.form.email,
                phone_number: this.form.phone_number
              }

              this.$store.commit('setContactUser', details)

              this.$v.$reset()
              this.form.adults = ''
              this.form.children = ''
              this.form.name = ''
              this.form.email = ''
              this.form.phone_number = ''
              this.form.message = ''

              this.dialog = false
              this.contactSnack = true
            }

            window.dataLayer.push({'event': 'BookingFormSubmitted'})
            this.sendingForm = false
          }, 1000)
        } catch (e) {
          throw new Error(e)
        }
      }
    },
    formatDate (date) { // and update active period
      if (!date) {
        return null
      }
      const dateFormatted = date.split('T')[0]
      each(this.page.boat.periods, (value, index) => {
        const periodDate = value.From
        if (periodDate.split('T')[0] === dateFormatted) {
          this.setBooking(dateFormatted, this.booking.weeks)
        }
      })

      return dateFormatted
    },
    /*
      Change the duration and combine multiple periods into one
    */
    changeDuration (weeks) {
      this.setBooking(this.booking.date, weeks)
      return weeks
    },
    /*
      updates the current active periods
    */
    setBooking (date, weeks) {
      let count = 0
      let periods = []
      this.booking.price = 0
      this.booking.basePrice = 0
      this.booking.date = date
      // get the periods
      each(this.page.boat.periods, (value, index) => {
        const periodDate = value.From
        if (count !== 0 && count < weeks) {
          periods.push(value)
          count++
        }
        if (periodDate.split('T')[0] === this.booking.date) {
          periods.push(value)
          count++
        }
      })

      // create a combined price and discount etc
      each(periods, (period, index) => {
        this.booking.price += period.PriceWithDiscount
        this.booking.basePrice += period.BasePrice
        periods[index].activeDate = period.From.split('T')[0]
      })
      this.booking.periods = periods

      this.booking.discount = Math.round((this.booking.basePrice - this.booking.price) / this.booking.basePrice * 100)
    }
  },
  props: [
    'isMobile',
    'page',
    'booking'
  ],
  data () {
    return {
      mobileBooking: false,
      form: {
        adults: '',
        children: '',
        name: '',
        email: '',
        phone_number: '',
        message: ''
      },
      dialog: false,
      sendingForm: false,
      modal: false,
      contactSnack: false,
      offsetTop: 0,
      dialogInfo: {
        name: '',
        model: '',
        base: '',
        duration: '',
        embarkation_date: '',
        price: '',
        currency: 'EUR'
      },
      duration: [
        {
          text: '1 week',
          value: 1
        },
        {
          text: '2 weeks',
          value: 2
        },
        {
          text: '3 weeks',
          value: 3
        },
        {
          text: '4 weeks',
          value: 4
        }
      ]
    }
  }
}
</script>