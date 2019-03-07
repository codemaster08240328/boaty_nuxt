<template>
  <section>
    <v-container grid-list-md>
      <draggable v-model="pageLinks" @end="drag('end')">
        <v-layout row wrap v-for="(link, index) in pageLinks" :key="link.id" v-if="link.page">
          <v-flex xs2>
            id: {{ link.id }}
          </v-flex>
          <v-flex xs3>
            link title: {{ link.title }}
          </v-flex>
          <v-flex xs3>
            page title: {{ link.page.title }}
          </v-flex>
          <v-flex xs3>
            page slug: {{ link.page.slug }}
          </v-flex>
          <v-flex xs1>
            <v-btn @click="deletePageLink(link, index)" color="warning">Delete?</v-btn>
          </v-flex>
        </v-layout>
      </draggable>
    </v-container>

    <v-container>
      <v-layout row wrap>
        <v-flex xs12>
          <v-select
            prepend-icon="fa-search"
            v-bind:items="pages"
            item-text="title"
            item-value="id"
            label="Select Your Page"
            autocomplete
            clearable
            v-model="addLink.page_id"
          >
            <template slot="item" slot-scope="data">
              <template v-if="typeof data.item !== 'object'">
                <v-list-tile-content v-text="data.item"></v-list-tile-content>
              </template>
              <template v-else>
                <v-list-tile-content>
                  <v-list-tile-title v-html="data.item.title"></v-list-tile-title>
                </v-list-tile-content>
              </template>
            </template>
          </v-select>

          <v-text-field
            v-model="addLink.title"
          ></v-text-field>
          <v-btn color="success" @click="addPageLink()">
            Add Page Link
          </v-btn>
        </v-flex>
      </v-layout>
    </v-container>
  </section>
</template>

<script>
import draggable from 'vuedraggable'
import _ from 'lodash'

export default {
  components: {
    draggable
  },
  props: [
    'pageLinks',
    'pages',
    'group_id'
  ],
  data () {
    return {
      addLink: {
        page_id: false,
        title: '',
        order: false
      },
      defaultLink: {
        page_id: false,
        title: '',
        order: false
      }
    }
  },
  methods: {
    async addPageLink () {
      console.log('add page link')
      this.addLink.order = this.pageLinks.length

      const { data } = await this.$axios.post(`/api/v1/sc-secret-admin/links/storelink/${this.group_id}`, this.addLink)
      this.pageLinks.push(data)
      console.log(this.pageLinks)
      console.log(data)
      this.addLink = Object.assign({}, this.defaultLink)
    },
    async deletePageLink (link, index) {
      if (window) {
        const confirm = window.confirm('Are you sure you want to delete this item?')
        if (confirm) {
          const { data } = await this.$axios.delete(`/api/v1/sc-secret-admin/links/deletelink/${link.group_id}/${link.page_id}`)
          if (data) {
            this.pageLinks.splice(index, 1)
          }
        }
      }
    },
    async editPageLink () {

    },
    async drag (e, i) {
      console.log('drag end')
      console.log(this.pageLinks)
      console.log(e, i)
      let ordered = []
      _.each(this.pageLinks, (value, index) => {
        ordered.push({
          page_id: value.page_id,
          order: index
        })
      })

      const { data } = await this.$axios.post(`/api/v1/sc-secret-admin/links/orderlinks/${this.group_id}`, { ordered: ordered })
      console.log(data)
    }
  }
}
</script>
