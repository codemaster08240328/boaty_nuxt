<template>
  <search-page-form :page_type="page_type"></search-page-form>
</template>

<script>
import SearchPageForm from '~/components/SearchPageForm'
export default {
  components: {
    SearchPageForm
  },
  data () {
    return {
      page_type: 'search'
    }
  }
}
</script>

<style>

</style>
