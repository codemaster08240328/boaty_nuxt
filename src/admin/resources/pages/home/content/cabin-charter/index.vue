<template>
  <div>
    <page-form :page_type="page_type"></page-form>
  </div>
</template>

<script>
import PageForm from '~/components/PageForm'
export default {
  components: {
    PageForm
  },
  data () {
    return {
      page_type: 'cabin-charter'
    }
  }
}
</script>