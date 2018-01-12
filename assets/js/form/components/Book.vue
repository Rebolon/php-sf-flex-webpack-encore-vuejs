<template>
    <q-item-main>
        <q-item-tile label>{{ book.title }}</q-item-tile>
    </q-item-main>
</template>

<script>
    import {
        QItemMain,
        QItemTile,
    } from 'quasar-framework'

    export default {
        name: 'Book',
        components: {
            QItemMain,
            QItemTile,
        },
        props: ['book'],
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
