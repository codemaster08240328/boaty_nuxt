<template>
  <section>
    <v-container>
      <v-layout row wrap>
        <v-flex xs8>
          <v-text-field
            v-model="editedItem.name"
            :error-messages="titleErrors"
            @input="$v.editedItem.name.$touch()"
            @blur="$v.editedItem.name.$touch()"
          ></v-text-field>
        </v-flex>
        <v-flex xs12>
          <v-btn @click="saveGroup(editedItem)" color="success">Add Group</v-btn>
        </v-flex>
      </v-layout>
    </v-container>

    <v-dialog v-model="dialog" fullscreen>
      <v-card>
        <v-card-title class="darken-3 cyan white--text">
          <span class="headline">{{ editedItem.name }}</span>
        </v-card-title>
        <v-card-text>
          <page-links :group_id="editedItem.id" :pageLinks="pageLinks" :pages="pages"></page-links>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" flat @click.native="close">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-container>
      <v-layout row wrap>
        <v-flex xs12>
          <v-data-table
            :headers="headers"
            :items="items"
            hide-actions
            class="elevation-1"
          >
            <template slot="items" slot-scope="props">
              <td>{{ props.item.id }}</td>
              <td>
                <v-edit-dialog
                  :return-value.sync="props.item.name"
                  lazy
                > {{ props.item.name }}
                  <v-text-field
                    slot="input"
                    v-model="props.item.name"
                    :rules="[max25chars]"
                    label="Edit"
                    single-line
                    counter
                    @change="editGroupName(props.item)"
                  ></v-text-field>
                </v-edit-dialog>
              </td>
              <td>{{ props.item.user_id }}</td>
              <td class="justify-center layout px-0">
                <v-btn icon class="mx-0" @click="editItem(props.item)">
                  <v-icon color="teal">edit</v-icon>
                </v-btn>
                <v-btn icon class="mx-0" @click="deleteGroup(props)">
                  <v-icon color="pink">delete</v-icon>
                </v-btn>
              </td>
            </template>
          </v-data-table>
        </v-flex>
      </v-layout>
    </v-container> 
  </section>
</template>

<script>
import PageLinks from '~/components/PageLinks'
import { validationMixin } from 'vuelidate'
import { required } from 'vuelidate/lib/validators'

export default {
  components: {
    PageLinks
  },
  mixins: [validationMixin],
  validations: {
    editedItem: {
      name: { required }
    }
  },
  methods: {
    async editItem (item) {
      this.editedIndex = this.items.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true

      const { data } = await this.$axios.get(`/api/v1/sc-secret-admin/links/links/${item.id}`)
      this.pageLinks = data
    },
    close () {
      this.dialog = false
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
        this.pageLinks = false
      }, 300)
    },
    async editGroupName (item) {
      await this.$axios.post(`/api/v1/sc-secret-admin/links/updategroup/${item.id}`, { name: item.name })
    },
    async deleteGroup (props) {
      if (window) {
        const confirm = window.confirm('Are you sure you want to delete this item?')

        if (confirm) {
          const { data } = await this.$axios.delete(`/api/v1/sc-secret-admin/links/deletegroup/${props.item.id}`)
          if (data) {
            this.items.splice(props.index, 1)
          }
        }
      }
    },
    async saveGroup (editedItem) {
      this.$v.$touch() // touching the form ensures validation occurs
      if (!this.$v.editedItem.$invalid) {
        // due to how v-select works we need to create an associative array
        this.saving = true
        try {
          console.log(editedItem)
          const { data } = await this.$axios.post(`/api/v1/sc-secret-admin/links/storegroup`, editedItem)
          console.log(data)
          setTimeout(() => {
            this.items.push(data)
            this.saving = false
            this.close()
          }, 1000)
        } catch (e) {
          throw new Error(e)
        }
      }
    }
  },
  async asyncData ({ app }) {
    let items = await app.$axios.get('/api/v1/sc-secret-admin/links/groups')
    let pages = await app.$axios.get('/api/v1/sc-secret-admin/links/pagetitles')
    return {
      items: items.data,
      pages: pages.data
    }
  },
  data () {
    return {
      max25chars: (v) => v.length <= 25 || 'Input too long!',
      dialog: false,
      headers: [
        { text: 'ID', value: 'id' },
        {
          text: 'Name',
          align: 'left',
          sortable: true,
          value: 'name'
        },
        { text: 'user_id', value: 'user_id', sortable: false }
      ],
      items: [],
      editedIndex: -1,
      editedItem: {
        name: ''
      },
      defaultItem: {
        name: ''
      },
      pageLinks: false,
      pageLink: false,
      saving: false
    }
  },
  computed: {
    titleErrors () {
      const errors = []
      if (!this.$v.editedItem.name.$dirty) return errors
      !this.$v.editedItem.name.required && errors.push('name is required.')
      return errors
    }
  }
}
</script>
