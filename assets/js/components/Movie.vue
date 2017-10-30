<template>
  <div class="movie">
    <h1>{{ msg }}</h1>

    <div>
      <strong>"{{ movie.title }}"</strong> : {{ movie.description }}<br />

      <cite>The movie has been released in {{ movie.release_date }}</cite>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Movie',
  props: ['id'],
  data() {
    return {
      msg: 'Detail of the movie',
      movie: {},
    };
  },
  created() {
    if (this.id === undefined) {
      return
    }

    const uri = `https://ghibliapi.herokuapp.com/films/${this.id}`
    fetch(uri)
        .then(res => res.json())
        .then(res => {
            this.movie = res
        })
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
ul {
  list-style-type: none;
  padding: 0;
}
</style>
