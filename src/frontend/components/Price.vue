<template>
  <section>
    <div v-show="price > 0">
      <span v-show="!symbol" class="currency">{{ defaultCurrency }} </span>
      <span v-show="symbol" class="currency">{{ symbol }}</span><span class="price">{{ price }}</span>
    </div>

    <div v-show="price == 0">
      <v-tooltip max-width="250" close-delay="500" bottom>
        <v-badge slot="activator" color="orange">
          <span slot="badge">?</span>
          <div class="price-on-request title">{{ $t('price.onrequest') }}</div>
        </v-badge>
        <span>{{ $t('price.explainrequest') }}</span>
      </v-tooltip>
    </div>
  </section>
</template>

<script>
import Money from 'money'
export default {
  props: [
    'currency',
    'value'
  ],
  computed: {
    defaultCurrency () {
      Money.rates = this.$store.state.fx.rates
      Money.base = this.$store.state.fx.base

      this.price = Math.ceil(Money.convert(this.value,
        {
          from: this.originalCurrency,
          to: this.$store.getters.defaultCurrency
        }))

      const currencySymbols = {
        'USD': '$', // US Dollar
        'EUR': '€', // Euro
        'CRC': '₡', // Costa Rican Colón
        'GBP': '£', // British Pound Sterling
        'ILS': '₪', // Israeli New Sheqel
        'INR': '₹', // Indian Rupee
        'JPY': '¥', // Japanese Yen
        'KRW': '₩', // South Korean Won
        'NGN': '₦', // Nigerian Naira
        'PHP': '₱', // Philippine Peso
        'PLN': 'zł', // Polish Zloty
        'PYG': '₲', // Paraguayan Guarani
        'THB': '฿', // Thai Baht
        'UAH': '₴', // Ukrainian Hryvnia
        'VND': '₫' // Vietnamese Dong
      }

      if (currencySymbols[this.$store.getters.defaultCurrency] !== undefined) {
        this.symbol = currencySymbols[this.$store.getters.defaultCurrency]
      } else {
        this.symbol = false
      }

      return this.$store.getters.defaultCurrency
    }
  },
  data () {
    return {
      originalCurrency: this.currency,
      price: this.value,
      test: '',
      symbol: false
    }
  }
}
</script>
