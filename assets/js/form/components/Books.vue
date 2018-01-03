<template>
    <div class="books">
        <label class="title">
            <h5 class="title">List of books</h5>
            <h6 class="hint">But it needs to store something somewhere, or it won't be possible to display anything neither do anything in fact</h6>
        </label>
        <q-toolbar color="primary">
            <q-toolbar-title>
                {{ msg }}
            </q-toolbar-title>
        </q-toolbar>

        <q-list highlight>
            <q-item v-for="book in books" :key="book.id">
                <Book :book="book" @remove="remove(ev)"/>
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
    import Book from './Book.vue'
    import gql from 'graphql-tag'
    export default {
        name: 'Books',
        components: {
            QToolbar,
            QToolbarTitle,
            QList,
            QItem,
            QItemMain,
            QItemTile,
            QSpinnerCircles,
            Book,
        },
        data() {
            return {
                msg: 'List of books',
                isLoading: true,
                books: [],
                id: undefined,
                paginationUris: {}
            };
        },
        created() {
            this.getListByRest()
        },
        methods: {
          getListByRest() {
            const uri = '/api/books'
            fetch(uri)
            .then(res => res.json())
            .then(res => {
              this.books = res['hydra:member']
              this.isLoading = false

              if (undefined !== res['hydra:view']) {
                ['first', 'last', 'next', 'previous',].forEach(key => {
                  if (undefined !== res['hydra:view'][`hydra:${key}`]) {
                    this.paginationUris[key] = res['hydra:view'][`hydra:${key}`]
                  }
                })
              }
            })
          },
        },
        apollo: {
          // @todo set variables in query to allow pagination navigation
          getList: gql`{
  books {
    edges {
      node {
        title
      }
    }
  }
}`,
        }
    };
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
