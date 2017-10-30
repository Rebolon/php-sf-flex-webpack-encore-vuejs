<template>
  <div class="movies">
    <h1>{{ msg }}</h1>

    <h2>Movies</h2>

    <ul v-if="!isLoading">
      <li v-for="movie in movies" v-on:click="showMovie($event)">
        <router-link :to="{name: 'Movie', params: { id: movie.id } }">{{ movie.title }}</router-link>
      </li>
    </ul>
    <Loader v-else="!isLoading"></Loader>
  </div>
</template>

<script>
export default {
  name: 'Movies',
  data() {
    return {
      msg: 'List of Ghibli movies',
      isLoading: true,
      movies: [],
      id: undefined,
    };
  },
  created() {
    const uri = 'https://ghibliapi.herokuapp.com/films'
    fetch(uri)
        .then(res => res.json())
        .then(res => {
            this.movies = res
            this.isLoading = false
        })
  },
  methods: {
    showMovie(ev) {
      console.log(ev, 'todo route to the movie page')
    },
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
