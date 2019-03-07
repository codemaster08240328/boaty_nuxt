<template>
  <v-container>
    <v-layout row wrap>
      <v-flex offset-sm2 sm4 xs12>
        <div v-for="(chequer) in chequers.slice(0, 4)" :key="chequer.id">
          <v-icon style="color: #c75c5c" class="mr-1">explore</v-icon> <a :href="`#chequer-${chequer.id}`">{{ chequer.title }}</a>
        </div>
      </v-flex>
      <v-flex sm4 xs12>
        <div v-for="(chequer) in chequers.slice(4)" :key="chequer.id">
          <v-icon style="color: #c75c5c" class="mr-1">explore</v-icon> <a :href="`#chequer-${chequer.id}`">{{ chequer.title }}</a>
        </div>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
export default {
  props: [
    'chequers'
  ]
}
</script>