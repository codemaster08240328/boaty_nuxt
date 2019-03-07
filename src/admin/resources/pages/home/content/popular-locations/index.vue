<template>
  <section>
    <ul>
      <li v-for="(country, index) in countries" :key="country.Name">
        <div class="headline">{{ country.Name }}
          <div class="body-2">
            <v-switch @click="updateCountry(index)" v-model="countries[index].Popular" color="primary"></v-switch>
          </div>
        </div>
      </li>
    </ul>
  </section>
</template>

<script>
export default {
  middleware: 'auth',
  methods: {
    async updateCountry (index) {
      console.log('updating country')
      this.countries[index].Popular = (this.countries[index].Popular) ? 0 : 1
      const ID = this.countries[index].ID
      const Popular = this.countries[index].Popular

      try {
        const { data } = await this.$axios.post('/api/v1/sc-secret-admin/content/popularcountry', { ID, Popular })

        console.log(data)
      } catch (e) {
        throw new Error(e)
      }
    }
  },
  async asyncData ({ params, app }) {
    let countries = []

    try {
      let { data } = await app.$axios.get('/api/v1/sc-secret-admin/content/countries')
      countries = data
    } catch (e) {
      console.log(e)
    }

    return {
      countries: countries
    }
  },
  data () {
    return {
      countries: [],
      truthy: 1
    }
  }
}
</script>


<style scoped>

</style>