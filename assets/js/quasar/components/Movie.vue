<template>
    <div class="movie">
        <q-toolbar color="primary">
            <q-toolbar-title>
                {{ msg }}
            </q-toolbar-title>
        </q-toolbar>

        <q-card v-if="movie">
            <q-card-title>
                {{ movie.title }}
            </q-card-title>
            <q-card-separator/>
            <q-card-main>
                <p>{{ movie.description }}
                <p>
                <p class="text-faded">The movie has been released in {{ movie.release_date }}</p>
            </q-card-main>
            <q-card-separator/>
            <q-card-actions>
                <q-btn flat color="primary" @click="$router.push({name: 'Movies'})">back</q-btn>
            </q-card-actions>
        </q-card>

        <q-spinner-circles v-if="isLoading" size="20px"/>
    </div>
</template>

<script>
import {
    QToolbar,
    QToolbarTitle,
    QCard,
    QCardTitle,
    QCardSeparator,
    QCardMain,
    QCardActions,
    QBtn,
    QSpinnerCircles,
} from 'quasar-framework'

export default {
    name: 'Movie',
    components: {
        QToolbar,
        QToolbarTitle,
        QCard,
        QCardTitle,
        QCardSeparator,
        QCardMain,
        QCardActions,
        QBtn,
        QSpinnerCircles,
    },
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
            return
        }

        const uri = `https://ghibliapi.herokuapp.com/films/${this.id}`
        fetch(uri)
            .then(res => res.json())
            .then(res => {
                this.movie = res
                this.isLoading = false
            })
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
