<template>
    <div class="movie">
        <h1>{{ msg }}</h1>

        <div v-if="!isLoading">
            <strong>"{{ movie.title }}"</strong> : {{ movie.description }}<br/>

            <cite>The movie has been released in {{ movie.release_date }}</cite>
        </div>

        <Loader v-else="!isLoading"></Loader>

        <router-link :to="{name: 'Movies'}" class="btn btn-primary">back</router-link>
    </div>
</template>

<script>
import axios from 'axios'
export default {
    name: 'Movie',
    props: ['id'],
    data() {
        return {
            msg: 'Detail of the movie',
            isLoading: true,
            movie: {},
        }
    },
    created() {
        if (this.id === undefined) {
            this.isLoading = false

            return
        }

        const uri = `https://ghibliapi.herokuapp.com/films/${this.id}`
        axios.get(uri)
            .then(res => {
                this.movie = res.data
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
