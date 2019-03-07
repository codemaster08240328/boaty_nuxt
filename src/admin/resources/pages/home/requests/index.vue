<template>
  <section>
    <v-container fluid>
      <v-layout row wrap>
        <v-flex xs 12>
          <v-alert color="info" icon="info" :value="true">
            requests
            <p>Boat price is in euro, exchange rate is the rate at the time of request </p>
          </v-alert>
        </v-flex>
      </v-layout>
      <v-layout row wrap>
        <v-dialog v-model="dialog" max-width="800px">
          <v-card>
            <v-card-title class="darken-3 cyan white--text">
              <span class="headline">{{ editedItem.name }}</span>
            </v-card-title>
            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs12 class="title">
                    Contact Details
                  </v-flex>
                  <v-flex xs6>
                    Contact Name: {{ editedItem.name }}
                  </v-flex>
                  <v-flex xs6>
                    Contact Email: {{ editedItem.email }}
                  </v-flex>
                
                  <v-flex xs12>
                    Mobile: {{ editedItem.phone_number }}
                  </v-flex>
                  <v-flex xs12>
                    Message: {{ editedItem.message }}
                  </v-flex>
                  <v-flex xs12>
                    contacted at {{ editedItem.created_at }}
                  </v-flex>
                  <v-flex class="title" xs12>
                    Financial & Boat Details
                  </v-flex>
                  <v-flex xs6>
                    Boat URL: {{ editedItem.url }}
                  </v-flex>
                  <v-flex xs6>
                    Charter date: {{ editedItem.date }}
                  </v-flex>
                  <v-flex xs6>
                    Boat Price (in euro): {{ editedItem.boat_price_euro }}
                  </v-flex>
                  <v-flex xs6>
                    Duration (in weeks): {{ editedItem.weeks }}
                  </v-flex>


                  <v-flex xs12>
                    Requested Currency: {{ editedItem.currency }}
                  </v-flex>
                  <v-flex xs12>
                    Exchange Rate (on {{ editedItem.created_at }}): {{ editedItem.exchange_rate }} to 1 euro
                  </v-flex>

                  <v-flex xs12 class="title">
                    Meta Details
                  </v-flex>
                  <v-flex xs4>
                    ip: {{ editedItem.ip }}
                  </v-flex>
                  <v-flex xs4>
                    country: {{ editedItem.country }}
                  </v-flex>
                  <v-flex xs4>
                    region: {{ editedItem.region }}
                  </v-flex>
                  <v-flex xs4>
                    city: {{ editedItem.city }}
                  </v-flex>
                  <v-flex xs4>
                    device: {{ editedItem.device }}
                  </v-flex>
                  <v-flex xs4>
                    browser: {{ editedItem.browser }}
                  </v-flex>
                  <v-flex xs12>
                    user agent: {{ editedItem.user_agent }}
                  </v-flex>

                  <v-flex xs12 class="title">
                    Sales Representative
                  </v-flex>

                  <v-flex xs12>
                    Matthew Gould
                  </v-flex>
                </v-layout>
              </v-container>

            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" flat @click.native="close">Cancel</v-btn>
              <v-btn color="blue darken-1" flat @click.native="save">Save</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-data-table
          :headers="headers"
          :items="items"
          hide-actions
          class="elevation-1"
        >
          <template slot="items" slot-scope="props">
            <td>{{ props.item.id }}</td>
            <td>{{ props.item.name }}</td>
            <td class="text-xs-right">{{ props.item.email }}</td>
            <td class="text-xs-right">{{ props.item.url }}</td>
            <td class="text-xs-right">{{ props.item.currency }}</td>
            <td class="text-xs-right">{{ props.item.date }}</td>
            <td class="text-xs-right">{{ props.item.boat_price_euro }}</td>
            <td class="justify-center layout px-0">
              <v-btn icon class="mx-0" @click="editItem(props.item)">
                <v-icon color="teal">edit</v-icon>
              </v-btn>
              <v-btn icon class="mx-0" @click="deleteItem(props.item)">
                <v-icon color="pink">delete</v-icon>
              </v-btn>
            </td>
          </template>

        </v-data-table>

      </v-layout>
    </v-container>
  </section>
</template>

<script>
export default {
  methods: {
    editItem (item) {
      this.editedIndex = this.items.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },
    async deleteItem (item) {
      const index = this.items.indexOf(item)
      await this.$axios.post(`/api/v1/sc-secret-admin/contacts/delete/${item.id}`)
      confirm('Are you sure you want to delete this item?') && this.items.splice(index, 1)
    },
    close () {
      this.dialog = false
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      }, 300)
    },
    save () {
      if (this.editedIndex > -1) {
        Object.assign(this.items[this.editedIndex], this.editedItem)
      } else {
        this.items.push(this.editedItem)
      }
      this.close()
    }
  },
  async asyncData ({ app }) {
    let { data } = await app.$axios.get('/api/v1/sc-secret-admin/contacts')
    return {
      items: data.reverse()
    }
  },
  data () {
    return {
      dialog: false,
      headers: [
        { text: 'ID', value: 'id' },
        {
          text: 'Name',
          align: 'left',
          sortable: false,
          value: 'name'
        },
        { text: 'Email', value: 'email' },
        { text: 'Boat URL', value: 'url' },
        { text: 'Currency', value: 'currency' },
        { text: 'Date', value: 'updated_at' },
        { text: 'Price At Date', value: 'boat_price_euro', sortable: false }
      ],
      items: [],
      editedIndex: -1,
      editedItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0
      },
      defaultItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0
      }
    }
  }
}
</script>
