<template>
    <div class="movie">
        <div v-if="canDisplayMovie()">
            <h1>{{ msg }}</h1>

            <div v-if="!isLoading">
                <strong>"{{ movie.title }}"</strong> : {{ movie.description }}<br/>

                <cite>The movie has been released in {{ movie.release_date }}</cite>
            </div>
        </div>

        <div v-else>
            No movie to display
        </div>

        <Loader v-if="isLoading"></Loader>

        <router-link :to="{name: 'Movies'}" class="btn btn-primary">back</router-link>
    </div>
</template>

<script>
import axios from 'axios'
export default {
    name: 'Movie',
    props: {
        id: String,
        aMovie: {
            type: Object,
            validator: movie => {
                const toTest = Object.keys(movie)

                if (!toTest.length) {
                    console.warn('no keys in movie')
                    return false
                }

                const validProps = [
                    'id', 'title', 'description', 'director', 'producer', 'release_date', 'rt_score', 'people',
                    'species', 'locations', 'vehicles', 'url', ]
                const mandatoryProps = ['id', 'title', 'description', 'release_date', ]

                const hasInvalidProps = toTest.filter(prop => !validProps.includes(prop)).length
                const hasAllMandatoryProps = mandatoryProps.length === toTest.filter(prop => mandatoryProps.includes(prop)).length

                return !hasInvalidProps && hasAllMandatoryProps
            }
        }
    },
    data() {
        return {
            msg: 'Detail of the movie',
            isLoading: true,
            movie: this.aMovie ? this.aMovie : {}
        }
    },
    created() {
        if (Object.keys(this.movie).length
            || this.id === undefined) {
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
    methods: {
        canDisplayMovie() {
            if (Object.keys(this.movie).length
                || this.id !== undefined) {
                return true
            }

            return false
        }
    }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
ul {
    list-style-type: none;
    padding: 0;
}
</style>
