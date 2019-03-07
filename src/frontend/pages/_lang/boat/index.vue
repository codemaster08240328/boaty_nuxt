<script>
export default {
  fetch ({ params, redirect, app }) {
    redirect(301, app.i18n.path('/search/'))
  }
}
</script>
