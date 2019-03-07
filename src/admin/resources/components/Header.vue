<template>
  <header>
    <v-navigation-drawer
          clipped
          v-model="drawer"
          app
          dark
          permanent
    >
    <v-expansion-panel dark>
      <v-expansion-panel-content dark>
        <div slot="header"><v-icon>place</v-icon>Content</div>
        <v-card dark>
          <v-card-text v-for="item in contentTypes" :key="item.text">
            <nuxt-link :to="item.path">{{ item.text }}</nuxt-link>
          </v-card-text>
        </v-card>
      </v-expansion-panel-content>
    </v-expansion-panel>

      <v-list>
        <v-list-tile v-for="item in items" :key="item.text">
          <v-list-tile-action>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <div v-if="item.offsite">
              <nuxt-link :to="item.path">
                <v-list-tile-title>
                  {{ item.text }}
                </v-list-tile-title>
              </nuxt-link>
            </div>
            <div v-else>
              <a :href="item.path">
                <v-list-tile-title>
                  {{ item.text }}
                </v-list-tile-title>
              </a>
            </div>
          </v-list-tile-content>
        </v-list-tile>
        <!-- <v-subheader class="mt-3 grey--text text--darken-1">Subheader</v-subheader>
        <v-list-tile @click="">
          <v-list-tile-action>
            <v-icon class="grey--text text--darken-1">settings</v-icon>
          </v-list-tile-action>
          <v-list-tile-title class="grey--text text--darken-1">Manage</v-list-tile-title>
        </v-list-tile> -->
      </v-list>
    </v-navigation-drawer>
    <v-toolbar class="yellow darken-4" prominent fixed clipped-left app >
      <v-toolbar-title style="width: 300px" class="ml-0 pl-3">
        <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
      </v-toolbar-title>
      <v-layout row align-center style="max-width: 650px">
        <!-- <simple-search/> -->
      </v-layout>
    </v-toolbar>
  </header>
</template>

<script>
export default {
  data () {
    return {
      drawer: false,
      items: [
        { icon: 'security', text: 'Bookings', offsite: true, path: '/home/bookings' },
        { icon: 'list', text: 'Reviews', offsite: true, path: '/home/reviews' },
        { icon: 'directions_boat', text: 'Boats', offsite: true, path: '/home/boats' },
        { icon: '', text: 'Requests', offsite: true, path: '/home/requests' }
      ],
      contentTypes: [
        { text: 'Yacht Charter', path: '/home/content/yacht-charter/' },
        { text: 'Search', path: '/home/content/search/' },
        { text: 'Cabin Charter', path: '/home/content/cabin-charter' },
        { text: 'Flotilla', path: '/home/content/flotilla' },
        { text: 'Last Minute', path: '/home/content/last-minute-sailing' },
        { text: 'Sailing Itineraries', path: '/home/content/sailing-itineraries' },
        { text: 'Bareboat Charter', path: '/home/content/bareboat-charter' },
        { text: 'Crewed Charter', path: '/home/content/crewed-charter' },
        { text: 'Catamaran Charter', path: '/home/content/catamaran-charter' },
        { text: 'Luxury Yacht Charter', path: '/home/content/luxury-yacht-charter/' },
        { text: 'Blogs', path: '/home/content/blogs' },
        { text: 'Links', path: '/home/content/links' }
      ]
    }
  },
  props: {
    source: String
  },
  methods: {

  }
}
</script>
<style scoped>
.orange {
  padding-top: 10px;
}
</style>