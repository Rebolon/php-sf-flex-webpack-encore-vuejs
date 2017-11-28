<template>
    <q-item-main>
        <q-item-tile label>{{ todo.title }}<TodoRemove :id="todo.id" @remove="remove(ev)"></TodoRemove></q-item-tile>
    </q-item-main>
</template>

<script>
    import {
        QItemMain,
        QItemTile
    } from 'quasar-framework'
    import TodoRemove from './TodoRemove.vue'
    export default {
        name: 'Todo',
        components: {
            QItemMain,
            QItemTile,
            TodoRemove,
        },
        props: ['todo'],
        methods: {
            remove(ev) {
                console.log(ev, 'todo route to the movie page')
                this.$emit('remove', this.id)
            },
        },
    };
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>