<template>
    <div class="movies">
        <h1>{{ msg }}</h1>

        <h2>Movies</h2>

        <ul v-if="!isLoading">
            <li v-for="movie in movies">
                <router-link :to="{name: 'Movie', params: { id: movie.id } }">{{ movie.title }}</router-link>
            </li>
        </ul>
        <Loader v-else></Loader>
    </div>
</template>

<script>
import axios from 'axios'
export default {
    name: 'Movies',
    data() {
        return {
            msg: 'List of Ghibli movies',
            isLoading: true,
            movies: [],
        }
    },
    created() {
        const uri = 'https://ghibliapi.herokuapp.com/films'
        axios.get(uri)
            .then(res => {
                this.movies = res.data
            })
            .catch((err) => {
                console.warn('error during http call', err)
            })
            .finally(() => {
                this.isLoading = false
            })
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
ul {
    list-style-type: none;
    padding: 0;
}
</style>
