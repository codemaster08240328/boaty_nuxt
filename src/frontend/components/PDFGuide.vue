<template>
  <v-layout class="pdf_guide" row wrap>
    <v-flex xs12>
    Enter Your Email Address To Download Our PDF pdf_guide
    <v-text-field
      light
        
      :label="$t('MultiBoatCard.email')"
      :hint="$t('MultiBoatCard.emailhint')"
      persistent-hint
      required

      ></v-text-field>
    </v-flex>
  </v-layout>
</template>

<script>
export default {
  props: [
    'locations'
  ],
  data () {
    return {
      s3: process.env.s3.url,
      s3Loc: process.env.s3.yachtCharterContent
    }
  }
}
</script>
