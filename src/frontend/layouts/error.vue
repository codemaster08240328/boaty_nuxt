<template>
  <section>
    <v-parallax
      class="yacht-charter"
      title="Error :(" 
      alt="Error :(" 
      src="https://s3.eu-west-2.amazonaws.com/sc30/static/404-error.jpg" height="600">
      <v-layout
        column
        align-center
        justify-center
        class="white--text"
      >
        <img src="https://s3.eu-west-2.amazonaws.com/sc30/static/sailchecker_logo_icon_only.svg" 
          alt="SailChecker Logo" title="SailChecker">
        <h1 class="white--text mb-2 display-1 text-xs-center">{{ error.message }}</h1>
        <h2 class="hidden-xs-only title mb-3 text-xs-center">Code: {{ error.statusCode }}</h2>
        <div class="subheading">
          <simple-search :lightText="true"></simple-search>
        </div>
      </v-layout>
      <v-layout class="mb-2 parallax-psp hidden-sm-and-down" row wrap>
        <v-flex xs2 offset-xs1>
          <div class="mb-1">
            <h4 class="body-1">Our global partners</h4>
          </div>
          <div class="items">
            <div style="width: 0px; height: 100px;"></div>
            <!-- <img class="mx-1" width="100" src="https://s3.eu-west-2.amazonaws.com/sc30/static/payment/eis-insurance.png"/> -->
            <img class="mx-1" width="100" src="https://s3.eu-west-2.amazonaws.com/sc30/static/payment/pantaenius.png"/>
          </div>
        </v-flex>
        <v-flex xs6>
          <div class="mb-1">
            <h4 class="body-1">SailChecker in the press</h4>
          </div>
          <div class="items">
            <img class="mx-1" width="100" src="https://s3.eu-west-2.amazonaws.com/sc30/static/awards/yy-award.png"/>
            <img style="background: rgba(255, 255, 255, 1); padding: 3px;" class="mx-1" width="100" src="https://s3.eu-west-2.amazonaws.com/sc30/static/awards/independant.png"/>
            <img class="mx-1" width="100" src="https://s3.eu-west-2.amazonaws.com/sc30/static/awards/yy.png"/>
            <img class="mx-1" width="100" src="https://s3.eu-west-2.amazonaws.com/sc30/static/awards/MARINA.png"/>
            <img style="background: rgba(255, 255, 255, 1); padding: 1px;" class="mx-1" width="100" src="https://s3.eu-west-2.amazonaws.com/sc30/static/awards/st.png"/>
          </div>
        </v-flex>
      </v-layout>
    </v-parallax>
    <!-- <h1 class="display-1">
      {{ error.statusCode }}
    </h1>
    <h2 class="white--text info">
      {{ error.message }}
    </h2>
    <nuxt-link class="button" to="/" v-if="error.statusCode === 404">
      Homepage
    </nuxt-link> -->
  </section>
</template>

<style scoped>
.why-us p {
  margin: 0 auto;
  width: 80%
}
h3 {
  display: inline;
}
i {
  position: relative;
  top: 9px;
  right: 4px;
  font-size: 36px;
}
.parallax-psp {
  justify-content: center;
}
.parallax-psp .items {
  display: flex;
  align-items: center;
  justify-content: center flex-end;
}
</style>

<script>
import SimpleSearch from '~/components/SimpleSearch'

export default {
  components: {
    SimpleSearch
  },
  props: ['error']
}
</script>

<style scoped>
.title
{
  margin-top: 15px;
  font-size: 5em;
}
.info
{
  font-weight: 300;
  color: #9aabb1;
  margin: 0;
}
.button
{
  margin-top: 50px;
}
</style>
