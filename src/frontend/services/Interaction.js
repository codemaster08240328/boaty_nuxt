import { each } from 'lodash'
/** 
 * The interaction service is used to capture what a user does throughout the website - be it search, selecting a boat page, enquiring etc
 * Because of how nuxt SSR and SPA works, this service will store data using vuex and also cookies
*/
const Interaction = {
  setAdvancedSearch (selected, params, filters) {
    const post = {}

    post.country = params.country || 0
    post.area = params.area || 0
    post.base = params.base || 0
    if (selected.boatType) {
      post.boatType = selected.boatType
    }

    if (selected.date) {
      post.date = selected.date
    }

    post.prices = []
    post.lengths = []
    post.years = []

    for (const index of selected.prices) {
      post.prices.push(filters.prices.options[index - 1].base)
    }

    for (const index of selected.lengths) {
      post.lengths.push(filters.lengths[index - 1].base)
    }

    for (const index of selected.years) {
      post.years.push(filters.years[index - 1].base)
    }

    post.sortby = selected.sortBy || 5
    post.cabins = selected.cabins || false
    post.toilets = selected.toilets || false

    each(post, (value, index) => {
      if (post[index] && post[index].length === 0) {
        // console.log('delete', value, index)
        // delete post[index]
      }

      if (post[index] === false) {
        // console.log('delete', value, index)
        // delete post[index]
      }
    })

    return post
  }
}

export default Interaction
