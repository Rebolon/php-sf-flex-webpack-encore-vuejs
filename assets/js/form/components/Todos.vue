<template>
    <div class="todos">
        <q-toolbar color="primary">
            <q-toolbar-title>
                {{ msg }}
            </q-toolbar-title>
        </q-toolbar>

        <q-list highlight>
            <q-item v-for="todo in todos" :key="todo.id">
                <Todo :todo="todo" @remove="remove(ev)"/>
            </q-item>
        </q-list>

        <q-spinner-circles v-if="isLoading" size="150px"/>
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
        QSpinnerCircles
    } from 'quasar-framework'
    import Todo from './Todo.vue'
    export default {
        name: 'Todos',
        components: {
            QToolbar,
            QToolbarTitle,
            QList,
            QItem,
            QItemMain,
            QItemTile,
            QSpinnerCircles,
            Todo,
        },
        data() {
            return {
                msg: 'List of todos',
                isLoading: true,
                todos: [],
                id: undefined,
            };
        },
        created() {
            const uri = '/api/todos'
            fetch(uri)
                .then(res => res.json())
                .then(res => {
                    this.todos = res
                    this.isLoading = false
                })
        },
        methods: {
            remove(ev) {
                console.log(ev, 'todo route to the movie page')
                const idx = this.todos.findIndex(todo => todo.id === ev)
                this.todos.splice(idx, 1)
            },
        },
    };
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>