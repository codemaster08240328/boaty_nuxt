<template>
  <section>
    <v-container fluid>
      <v-layout row wrap>
        <v-flex xs 12>
          <v-alert color="info" icon="info" v-model="alerts">
            This section of the admin allows you to moderate reviews.
            <!-- In this section of the admin, you are able to add, edit and delete reviews. <br>
            Reviews may be added as one of the following: site reviews, location review, boat review <br>

            A site review is a generic review about SailChecker and our awesome servivice <br>
            A location review is simply a review that is about a specific location e.g., Malta<br>
            A boat review is simply a review that is about a specific boat e.g., Sun Odyssey 509 <br>

            A location review for Malta will appear in malta-majjistral however malta-majjistral will take priority<br>

            A single review can be tagged as site_review, location, boat. The nature of this system means a review cannot have multiple locations or boats -->
          </v-alert>
        </v-flex>
      </v-layout>
    </v-container>

    <v-container grid-list-md fluid>
      <v-layout row wrap>
        <v-flex xs6 v-for="review in reviews" v-bind:key="review.ID">
          <v-card>
            <v-card-title class="cyan darken-3 white--text" primary-title>
              Review ID: {{ review.id }}
              <span v-show="review.status == 1" class="green">APPROVED</span>
            </v-card-title>
            <v-card-text class="body-2">
              <div>Full Name: {{ review.name }}</div>
              <div>Rating (out of 5): {{ review.rating }}</div>
              <div>Email: {{ review.email }}</div>
              <div class="subheading">Message: {{ review.message }}</div>
            </v-card-text>
            <v-card-text>
              Boat URL: <a target="_blank" :href="'https://sailchecker.com' + review.url">BOAT PAGE</a>
            </v-card-text>
            <v-card-text class="cyan white--text">
              <h5>Review Meta</h5>
              <div>ip: {{ review.ip }}</div>
              <div>country: {{ review.country }}</div>
              <div>status: {{ review.status }}</div>
              <div>submitted on: {{ review.created_at }}</div>
            </v-card-text>
            <v-card-actions>
              <v-btn @click="submitReview(review, 1)" color="green">Approve</v-btn>
              <v-btn @click="submitReview(review, 0)" color="red">Disapprove</v-btn>
            </v-card-actions>
          </v-card>
        </v-flex>
      </v-layout>
    </v-container>
  </section>
</template>

<script>
export default {
  data () {
    return {
      alerts: true,
      reviews: []
    }
  },
  methods: {
    async submitReview (review, status) {
      review.status = status
      let { data } = await this.$axios.post('api/v1/sc-secret-admin/reviews/update', review)
      console.log(data)
    }
  },
  async asyncData ({ app }) {
    let page = {}
    try {
      let { data } = await app.$axios.get('api/v1/sc-secret-admin/reviews')
      page.data = data
    } catch (e) {
      throw new Error(e)
    }
    return {
      reviews: page.data
    }
  }
}
</script>
