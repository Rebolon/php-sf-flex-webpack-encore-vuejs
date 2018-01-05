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
            this.addBook()
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

          addBook() {
            const newBook = {
              "title": "test livre",
              "description": "test livre",
              "indexInSerie": 1,
              "clientMutationId": "0",
              "serie": {
                "name": "test serie",
                "clientMutationId": "1"
              }
            }

            this.$apollo.mutate({
              // Query
              mutation: gql`mutation createBook(
                                $book: createBookInput!
                            ) {

                                createBook(
                                    input: $book
                                ) {
                                    title
                                    id
                                    serie {
                                      name
                                      id
                                    }
                                }
                        }`,
              // Parameters
              variables: {
                book: newBook,
              },
              // Update the cache with the result
              // The query will be updated with the optimistic response
              // and then with the real result of the mutation
              update: (store, { data: { newBook } }) => {
                console.log(store, arguments[1])
                //// Read the data from our cache for this query.
                //const data = store.readQuery({ query: TAGS_QUERY })
                //// Add our tag from the mutation to the end
                //data.tags.push(newTag)
                //// Write our data back to the cache.
                //store.writeQuery({ query: TAGS_QUERY, data })
              },
              // Optimistic UI
              // Will be treated as a 'fake' result as soon as the request is made
              // so that the UI can react quickly and the user be happy
              //optimisticResponse: {
              //  __typename: 'Mutation',
              //  createBook: {
              //    __typename: 'Book',
              //    id: -1,
              //    label: book,
              //  },
              //},
            }).then((data) => {
              // Result
              console.log(data)
            }).catch((error) => {
              // Error
              console.error("Books.vue error catched by apollo client: ", error)
              //// We restore the initial user input
              //this.newTag = newTag
            })
          }
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
