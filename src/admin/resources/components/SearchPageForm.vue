<template>
    <v-container fluid>
    <v-layout row wrap>
      <v-dialog v-model="dialog" hide-overlay fullscreen>
        <v-btn color="primary" dark slot="activator" class="mb-2">New Item</v-btn>
        <v-card>
          <v-card-title class="darken-3 cyan white--text">
            <v-btn icon @click.native="close" dark>
              <v-icon>close</v-icon>
            </v-btn>
            <v-toolbar-title><span class="headline">{{ editedItem.title }}</span></v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items>
              <v-btn dark flat @click="savePage(editedItem)">Save</v-btn>
            </v-toolbar-items>
          </v-card-title>
          <v-card-text>
            <v-container fluid grid-list-md>
              <v-layout wrap>
                <v-flex class="pb-2" xs12 v-show="editedItem.slug">
                  <span class="title">Preview URL: 
                    <a target="_blank" :href="`/preview/${page_type}/${editedItem.slug}`">{{ editedItem.slug }}</a>
                  </span>
                </v-flex>
                <v-flex class="pb-2" xs12 v-if="editedItem.user">
                  <span class="body-2">Author: 
                    {{ editedItem.user.full_name }}
                  </span>
                </v-flex>
                <v-flex xs8>
                  <v-text-field
                    prepend-icon="fa-header"
                    label="Title"
                    hint="Used as the pages singular H1 tag and title attribute for SEO"
                    persistent-hint
                    v-model="editedItem.title"
                    :error-messages="titleErrors"
                    @input="$v.editedItem.title.$touch()"
                    @blur="$v.editedItem.title.$touch()"
                    :counter="85"
                    readonly
                    required
                  ></v-text-field>

                  <v-text-field
                    prepend-icon="fa-google"
                    label="Description"
                    hint="Used as a subtitle and also a description for google, and social media"
                    persistent-hint
                    v-model="editedItem.description"
                    :error-messages="descriptionErrors"
                    @input="$v.editedItem.description.$touch()"
                    @blur="$v.editedItem.description.$touch()"
                    :counter="160"
                    required
                  ></v-text-field>
                  <no-ssr>
                    <div ref="main-body"
                      id="main-body"
                      class="quill-editor" 
                      :content="editedItem.body"
                      v-quill:main="editorOption"
                      @change="onEditorChange($event)"
                    >
                    </div>
                  </no-ssr>
                  <div>{{ editedItem.body.length }} characters</div>
                  <div class="white--text orange darken-4 title pa-2" v-show="!$v.editedItem.body.required">body is required</div>
                  <div class="white--text orange darken-4 title pa-2" v-show="!$v.editedItem.body.minLength">The body must be at least 1000 characters</div>

                  <v-container grid-list-md>
                    <v-layout row wrap>
                      <v-flex xs12>
                        <v-card>
                          <v-card-title class="cyan darken-3 white--text" primary-title>
                            <h4>Page Images</h4>
                          </v-card-title>
                          <v-card-title>
                            * Images resized to 1600 * 800 (recommended is this size!) <br/>
                            * Images automatically smushed and turned into medium and thumbnails  <br/>
                            * Select an image as the hero image (defaults to first image) <br/>
                          </v-card-title>

                          <v-card-title>
                            <ul>
                              <li v-for="file in files" :key="file.id">
                                <span>{{file.name}}</span> -
                                <span>{{file.size }}</span> -
                                <span v-if="file.error" class="white--text orange darken-4 pa-2">{{file.error}}</span>
                                <span v-else-if="file.success">success</span>
                                <span v-else-if="file.active">active</span>
                                <span v-else-if="file.response.code == 'EEXIST'">active</span>
                                <span v-else></span>
                              </li>
                            </ul>
                          </v-card-title>
                          <v-card-actions>
                            <file-upload
                              class="btn btn-primary"
                              :post-action="`${apiBaseUrl}api/v1/sc-secret-admin/content/upload/${this.page_type}`"
                              extensions="gif,jpg,jpeg,png,webp"
                              accept="image/png,image/gif,image/jpeg,image/webp"
                              :headers="apiHeaders"
                              :multiple="true"
                              :size="1024 * 1024 * 1"
                              v-model="files"
                              @input-filter="inputFilter"
                              @input-file="inputFile"
                              ref="upload">
                              <i class="fa fa-plus"></i>
                              Select files
                            </file-upload>
                            <button type="button" class="btn btn-success" v-if="!$refs.upload || !$refs.upload.active" @click.prevent="$refs.upload.active = true">
                              <i class="fa fa-arrow-up" aria-hidden="true"></i>
                              Start Upload
                            </button>
                          </v-card-actions>
                        </v-card>
                      </v-flex>

                      <v-flex xs12 sm4 v-for="(image, index) in editedItem.pageImage" :key="image.id" v-if="image.page_id > 0 || image.page_id == undefined">
                        <v-card>
                          <v-card-media
                            class="white--text"
                            height="200px"
                            :src="`https://s3.eu-west-2.amazonaws.com/sc30/uploads/${page_type}/${image.fileName}`">
                            <v-container fill-height fluid>
                              <v-layout fill-height>
                                <v-flex xs12 align-end flexbox>
                                  <span class="headline">{{ image.fileName }}</span>
                                </v-flex>
                              </v-layout>
                            </v-container>
                          </v-card-media>
                          <v-card-title>
                            <div>
                              <v-text-field
                                prepend-icon="fa-header"
                                label="File Name"
                                v-model="image.fileName"
                                readonly
                              ></v-text-field>
                              <v-text-field
                                label="Alt Text"
                                v-model="image.altText"
                                @input="$v.editedItem.pageImage.$each[index].altText.$touch()"
                                @blur="$v.editedItem.pageImage.$each[index].altText.$touch()"
                                required
                              ></v-text-field>

                              <div class="white--text orange darken-4 title pa-2" v-show="imageAltErrors(index).length != 0">
                                {{ imageAltErrors(index) }}
                              </div>

                              <v-text-field
                                label="Title Text"
                                v-model="image.titleText"
                                @input="$v.editedItem.pageImage.$each[index].titleText.$touch()"
                                @blur="$v.editedItem.pageImage.$each[index].titleText.$touch()"
                                required
                              ></v-text-field>

                              <div class="white--text orange darken-4 title pa-2" v-show="imageTitleErrors(index).length != 0">
                                {{ imageTitleErrors(index) }}
                              </div>

                              <v-checkbox label="Header image?"
                                          v-model="image.position"
                                          color="red darken-3"
                                          hide-details></v-checkbox>
                            </div>
                          </v-card-title>
                          <v-card-actions>
                            <v-btn @click="deleteImage(image, index)" flat color="red">Delete</v-btn>
                          </v-card-actions>
                        </v-card>
                      </v-flex>
                    </v-layout>
                  </v-container>
                </v-flex>

                <v-flex xs4>
                  <no-ssr>
                    <div>
                      <v-select
                        :items="countries"
                        item-text="Name"
                        item-value="ID"
                        v-model="editedItem.country_id"
                        label="Select Country"
                        autocomplete
                        @change="filterAreasByCountry($event)"
                        clearable
                      >
                      </v-select>
                      
                      <v-select v-show="editedItem.country_id"
                        :items="areas"
                        item-text="Name"
                        item-value="ID"
                        v-model="editedItem.area_id"
                        label="Select Area"
                        @input="filterBasesByAreas($event)"
                        clearable
                      >
                      </v-select>
                      <div v-show="editedItem.area_id">
                      <v-select
                        :items="bases"
                        item-text="Name"
                        item-value="ID"
                        v-model="editedItem.base_id"
                        label="Select Base"
                        autocomplete
                        clearable
                      >
                      </v-select>
                      </div>
                      <v-select
                        :items="$store.state.locales"
                        v-model="editedItem.locale"
                        label="Select a locale"
                        single-line
                        hint="Choose a locale"
                        persistent-hint
                      ></v-select>
                    </div>
                  </no-ssr>
                  <div v-show="saving">
                    <v-spacer></v-spacer>
                    <v-progress-circular indeterminate :size="70" :width="7" color="purple"></v-progress-circular>
                    <v-spacer></v-spacer>
                  </div>
                </v-flex>
              </v-layout>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click.native="close">Cancel</v-btn>
            <v-btn color="blue darken-1" flat @click="savePage(editedItem)">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-flex xs12>
        <v-data-table
          :headers="headers"
          :items="items"
          hide-actions
          class="elevation-1"
        >
          <template slot="items" slot-scope="props">
            <td class="text-xs-center">{{ props.item.id }}</td>
            <td class="text-xs-center">{{ props.item.title }}</td>
            <td class="text-xs-center">{{ props.item.updated_at }}</td>
            <td class="text-xs-center">{{ props.item.locale }}</td>
            <td class="text-xs-center" v-if="props.item.user">{{ props.item.user.fullName }}</td>
            <td class="text-xs-center" v-else></td>
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
      </v-flex>
    </v-layout>
    <v-snackbar
      :timeout="10000"
      bottom
      multi-line
      v-model="saveError"
    >
      Please fill in all fields before saving!
      <v-btn flat color="pink" @click.native="saveError = false">Close</v-btn>
    </v-snackbar>

  </v-container>
</template>

<script>
import { validationMixin } from 'vuelidate'
import { required, maxLength, minLength } from 'vuelidate/lib/validators'
import FileUpload from 'vue-upload-component'
import { each, map, findIndex } from 'lodash'

export default {
  props: [
    'page_type'
  ],
  components: {
    FileUpload
  },
  mixins: [validationMixin],
  validations: {
    editedItem: {
      title: { required, maxLength: maxLength(85) },
      description: { required, maxLength: maxLength(160) },
      body: { required, minLength: minLength(1000) },
      pageImage: {
        $each: {
          fileName: { required },
          altText: { required },
          titleText: { required }
        }
      }
    }
  },
  async created () {
    this.apiHeaders.Authorization = `Bearer ${this.$cookie.get('sc_auth')}`
    try {
      this.$axios.defaults.headers.Authorization = 'Bearer ' + this.$cookie.get('sc_auth')
      const countries = await this.$axios.get('/api/v1/search/countries')
      const regions = await this.$axios.get('/api/v1/search/regionsraw')
      const page = await this.$axios.get(`/api/v1/sc-secret-admin/content/list/${this.page_type}/`)
      this.items = page.data
      this.regions = regions.data
      this.countries = countries.data
    } catch (e) {
      throw new Error(e)
    }
  },
  data () {
    return {
      saveError: false,
      items: [],
      regions: [],
      countries: [],
      headers: [
        {
          text: 'Page ID',
          sortable: true,
          value: 'id',
          align: 'center'
        },
        {
          text: 'Title',
          value: 'title'
        },
        {
          text: 'Last Updated',
          value: 'updated_at'
        },
        {
          text: 'Locale',
          value: 'locale'
        },
        {
          text: 'Author',
          value: 'user'
        }
      ],
      editorOption: {
        modules: {
          toolbar: {
            container: [
              ['bold', 'italic', 'underline', 'strike'],
              [
                'blockquote',
                'code-block',
                'link',
                { 'header': [2, 3, 4, 5, 6, false] },
                { 'color': [] },
                { 'background': [] },
                { 'align': [] },
                { 'list': 'ordered' },
                { 'list': 'bullet' },
                'clean'
              ]
            ]
          }
        },
        placeholder: 'Compose an epic...',
        theme: 'snow'
      },
      dialog: false,
      files: [],
      apiBaseUrl: process.env.API_BASE_URL,
      apiHeaders: {
        'Authorization': ''
      },
      edit: false,
      cropper: false,
      saving: false,
      editedItem: {
        title: 'search-',
        body: '',
        description: '',
        pageImage: [],
        type: this.page_type,
        locale: 'en-us'
      },
      defaultItem: {
        title: 'search-',
        body: '',
        description: '',
        pageImage: [],
        type: this.page_type,
        locale: 'en-us'
      },
      areas: [],
      bases: []
    }
  },
  methods: {
    imageAltErrors (index) {
      const errors = []
      if (!this.$v.editedItem.pageImage.$each[index].altText.$dirty) return errors
      !this.$v.editedItem.pageImage.$each[index].altText.required && errors.push('Image alt is required')
      return errors
    },
    imageTitleErrors (index) {
      const errors = []
      if (!this.$v.editedItem.pageImage.$each[index].titleText.$dirty) return errors
      !this.$v.editedItem.pageImage.$each[index].titleText.required && errors.push('Image title is required')
      return errors
    },
    filterBasesByAreas (e) {
      if (e === null) {
        this.editedItem.country_id = 0
        this.editedItem.base_id = 0
      }
      let matches = []
      let bases = []
      each(this.regions, (value) => {
        if (value.area_id === e) {
          if (matches.indexOf(value.base_id) === -1) {
            bases.push({
              ID: value.base_id,
              Name: value.base_name
            })
            matches.push(value.base_id)
          }
        }
      })
      this.bases = bases
    },
    /**
      @description Takes an an area_id and returns a list of bases that belong to the area
      @param e integer
    */
    filterAreasByCountry (e) {
      if (e === null) {
        this.editedItem.area_id = 0
        this.editedItem.base_id = 0
      }
      let matches = []
      let areas = []
      each(this.regions, (value) => {
        if (value.country_id === e) {
          if (matches.indexOf(value.area_id) === -1) { // push the area if its unique
            areas.push({
              ID: value.area_id,
              Name: value.area_name_en
            })
            matches.push(value.area_id)
          }
        }
      })
      this.areas = areas
    },
    /** @description persists the editedItem to the database
     * 
     * @param editedItem Array
     */
    async savePage (editedItem) {
      this.$v.$touch() // touching the form ensures validation occurs
      if (!this.$v.editedItem.$invalid) {
        // due to how v-select works we need to create an associative array
        this.saving = true
        try {
          let title = 'search-'
          if (editedItem.base_id) {
            let index = findIndex(this.regions, (o) => {
              return o.base_id === editedItem.base_id
            })
            title += `${this.regions[index].country_name_en}-${this.regions[index].area_name_en}-${this.regions[index].base_name}`
          } else if (editedItem.area_id) {
            let index = findIndex(this.regions, (o) => {
              return o.area_id === editedItem.area_id
            })
            title += `${this.regions[index].country_name_en}-${this.regions[index].area_name_en}`
          } else if (editedItem.country_id) {
            let index = findIndex(this.regions, (o) => {
              return o.country_id === editedItem.country_id
            })
            title += this.regions[index].country_name_en
          }

          editedItem.title = title
          editedItem.template = 'standard'
          console.log('kk', editedItem)

          let pageData
          if (editedItem.id) {
            pageData = await this.$axios.post(`/api/v1/sc-secret-admin/content/update/${editedItem.id}`, editedItem)
          } else {
            pageData = await this.$axios.post(`/api/v1/sc-secret-admin/content/store`, editedItem)
          }

          setTimeout(() => {
            if (this.editedIndex > -1) {
              console.log(pageData)
              Object.assign(this.items[this.editedIndex], pageData.data)
            } else {
              this.items.push(pageData.data)
            }
            this.saving = false
            this.close()
          }, 1000)
        } catch (e) {
          console.log(e)
          throw new Error(e)
        }
      } else {
        this.saveError = true
      }
    },
    deleteImage (image, index) {
      if (image.page_id) {
        image.page_id = Math.abs(image.page_id) * -1
      } else {
        this.editedItem.pageImage.splice(index, 1)
      }
    },
    /**
     * @description because of v-select limitations, sort current categories boat types and brands into 1d array
     * @param item Array
     */
    editItem (item) {
      console.log('before', item.pageBoatType)

      this.selectedBoatTypes = map(item.pageBoatType, 'type_id')
      this.selectedBoatBrands = map(item.pageBoatBrand, 'brand_id')
      this.selectedCategories = map(item.pageCategory, 'category_id')

      this.filterAreasByCountry(item.country_id)
      this.filterBasesByAreas(item.area_id)

      this.editedIndex = this.items.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },
    deleteItem (item) {
      const index = this.items.indexOf(item)
      confirm('Are you sure you want to delete this item?') && this.items.splice(index, 1)
      try {
        this.$axios.delete(`api/v1/sc-secret-admin/content/delete/${item.id}`)
      } catch (e) {
        throw new Error(e)
      }
    },
    /**
     * @description closes the current dialog and resets editedItem and index
     * use a delay or the dialog doesn't fully close
     */
    close () {
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
        this.dialog = false
      }, 600)
    },
    onEditorChange (e) {
      this.editedItem.body = e.html
    },
    alert (message) {
      alert(message)
    },
    inputFile (newFile, oldFile) {
      if (newFile && !oldFile) {
        // add
        console.log('add', newFile)
      }
      if (newFile && oldFile) {
        // update
        console.log('update', newFile)
      }
      if (!newFile && oldFile) {
        // remove
        console.log('remove', oldFile)
      }
    },
    inputFilter (newFile, oldFile, prevent, test) {
      if (newFile && !oldFile) {
        if (!/\.(gif|jpg|jpeg|png|webp)$/i.test(newFile.name)) {
          this.alert('Your choice is not a picture')
          return prevent()
        }
      }

      if (newFile && oldFile) {
        // update
        console.log('update', newFile)
        if (newFile.progress === '100.00' && newFile.success === true) {
          const imageData = {
            fileName: newFile.response.fileName,
            altText: '',
            titleText: '',
            content: '',
            position: 'header'
          }

          this.editedItem.pageImage.push(imageData)
        }
      }

      if (newFile && (!oldFile || newFile.file !== oldFile.file)) {
        newFile.url = ''
        let URL = window.URL || window.webkitURL
        if (URL && URL.createObjectURL) {
          newFile.url = URL.createObjectURL(newFile.file)
        }
      }
    }
  },
  computed: {
    titleErrors () {
      const errors = []
      if (!this.$v.editedItem.title.$dirty) return errors
      !this.$v.editedItem.title.maxLength && errors.push('Title must be at most 85 characters')
      !this.$v.editedItem.title.required && errors.push('Title is required.')
      return errors
    },
    descriptionErrors () {
      const errors = []
      if (!this.$v.editedItem.description.$dirty) return errors
      !this.$v.editedItem.description.maxLength && errors.push('Description must be at most 160 characters')
      !this.$v.editedItem.description.required && errors.push('Description is required')
      return errors
    },
    bodyErrors () {
      const errors = []
      if (!this.$v.editedItem.body.$dirty) return errors
      !this.$v.editedItem.body.minLength && errors.push('body must be at minimum 1000 characters')
      !this.$v.editedItem.body.required && errors.push('Body is required')
      return errors
    }
  }
}
</script>

<style>
.dragArea {
  width: 100%;
}
.quill-editor {
  min-height: 200px;
  max-height: 400px;
  overflow-y: auto;
}


.ql-container {
  height: 0;
}
.ql-editor blockquote, .ql-editor h1, .ql-editor h2, .ql-editor h3, .ql-editor h4, .ql-editor h5, .ql-editor h6, .ql-editor ol, .ql-editor p, .ql-editor pre, .ql-editor ul {
  margin-bottom: 10px !important;
}

.example-avatar .avatar-upload .rounded-circle {
     max-height: 400px;
}
.example-avatar .text-center .btn {
  margin: 0 .5rem
}
.example-avatar .avatar-edit-image {
    max-height: 400px;
}
.example-avatar .drop-active {
  top: 0;
  bottom: 0;
  right: 0;
  left: 0;
  position: fixed;
  z-index: 9999;
  opacity: .6;
  text-align: center;
  background: #000;
}
.example-avatar .drop-active h3 {
  margin: -.5em 0 0;
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  -webkit-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
  font-size: 40px;
  color: #fff;
  padding: 0;
}
</style>
