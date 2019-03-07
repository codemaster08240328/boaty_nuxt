<template>
  <v-app
    id="inspire"
  >
  <!-- <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MZCT9W"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript> -->
    <div id="fb-root"></div>
    <my-header/>
    <main>
      <v-content>
        <v-container fluid style="margin: 0px; padding: 0px; cursor: pointer;" class="hidden-sm-and-down cyan darken-1 text-xs-center white--text body-2">
          <v-container>
            <v-layout row wrap>
              <v-flex xs12 md3>
                <v-tooltip bottom>
                  <div slot="activator">
                    <img class="mr-1" style="height: 40px; vertical-align:middle;" src="https://s3.eu-west-2.amazonaws.com/sc30/static/leading.svg" :alt="$t('usp.leading')"/>
                    <span>{{ $t('usp.leading') }}</span>
                  </div>
                  <span>{{ $t('usp.leadingdesc') }}</span>
                </v-tooltip>
              </v-flex>
              <v-flex xs12 md3>
                <v-tooltip bottom>
                  <div slot="activator">
                    <img class="mr-1" style="height: 40px; vertical-align:middle;" src="https://s3.eu-west-2.amazonaws.com/sc30/static/price.svg" :alt="$t('usp.price')"/>
                    <span>{{ $t('usp.price') }}</span>
                  </div>
                  <span>{{ $t('usp.pricedesc') }}</span>
                </v-tooltip>
              </v-flex>
              <v-flex xs12 md3>
                <v-tooltip bottom>
                  <div slot="activator">
                    <img class="mr-1" style="height: 40px; vertical-align:middle;" src="https://s3.eu-west-2.amazonaws.com/sc30/static/24.svg" :alt="$t('usp.service')"/>
                    <span>{{ $t('usp.service') }}</span>
                  </div>
                  <span>{{ $t('usp.servicedesc') }}</span>
                </v-tooltip>
              </v-flex>
              <v-flex xs12 md3>
                <v-tooltip bottom>
                  <div slot="activator">
                    <img class="mr-1" style="height: 40px; vertical-align:middle;" src="https://s3.eu-west-2.amazonaws.com/sc30/static/locationpin.svg" :alt="$t('usp.destination')"/>
                    <span>{{ $t('usp.destination') }}</span>
                  </div>
                  <span>{{ $t('usp.destinationdesc') }}</span>
                </v-tooltip>
              </v-flex>
            </v-layout>
          </v-container>
        </v-container>

        <!-- {{ $store.state.locale }}<br>

        {{ $store.state.user }} -->

        <nuxt/>
      </v-content>
    </main>
    <my-footer/>
  </v-app>
</template>

<script>
import MyHeader from '~/components/Header.vue'
import MyFooter from '~/components/Footer.vue'

export default {
  components: {
    MyFooter,
    MyHeader
  }
}
</script>
