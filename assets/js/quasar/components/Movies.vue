<template>
    <div class="movies">
        <q-toolbar color="primary">
            <q-toolbar-title>
                {{ msg }}
            </q-toolbar-title>
        </q-toolbar>

        <q-list highlight>
            <q-item v-for="movie in movies" :key="movie.id"
                    v-on:click="showMovie($event)"
                    :to="{name: 'Movie', params: { id: movie.id } }">
                <q-item-main>
                    <q-item-tile label>{{ movie.title }}</q-item-tile>
                </q-item-main>
            </q-item>
        </q-list>

        <q-spinner-circles v-if="isLoading" size="20px"/>
    </div>
</template>

<script>
    import {
        QToolbar,
        QToolbarTitle,
        QList,
        QListHeader,
        QItem,
        QItemMain,
        QItemTile,
        QSpinnerCircles,
    } from 'quasar-framework'

    export default {
        name: 'Movies',
        components: {
            QToolbar,
            QToolbarTitle,
            QList,
            QItem,
            QItemMain,
            QItemTile,
            QSpinnerCircles,
        },
        data() {
            return {
                msg: 'List of Ghibli movies',
                isLoading: true,
                movies: [],
                id: undefined,
            }
        },
        created() {
            const uri = 'https://ghibliapi.herokuapp.com/films'
            fetch(uri).then(res => res.json()).then(res => {
                this.movies = res
                this.isLoading = false
            })
        },
        methods: {
            showMovie(ev) {
                console.log(ev, 'todo route to the movie page')
            },
        },
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
