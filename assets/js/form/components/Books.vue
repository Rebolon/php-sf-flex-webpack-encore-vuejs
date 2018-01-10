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

        <q-spinner-circles v-if="isLoading" size="150px"/>

        <q-pagination v-if="books.length" v-model="pagination.page" :max="pagination.total" @change="getOtherPage($event)"></q-pagination>

        <q-list highlight>
            <q-item v-for="book in books" :key="book.id">
                <Book :book="book" @remove="remove(ev)"/>
            </q-item>
        </q-list>

        <q-pagination v-if="books.length" v-model="pagination.page" :max="pagination.total" @change="getOtherPage($event)"></q-pagination>

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
        QPagination
    } from 'quasar-framework'
    import { logout } from '../../login'
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
            QPagination,
            Book,
        },
        data() {
            return {
                msg: 'List of books',
                isLoading: true,
                books: [],
                id: undefined,
                pagination: {
                  page: 1,
                  total: 1,
                  uris: {},
                  itemPerPage: 10,
                  endCursor: '',
                  nexEndCursor: ''
                },

                // dataStore for GraphQL Queries: vue-apollo will inject data in vue components based on the name of the query
                getBooks: {
                  pageInfo: {}
                },
            };
        },
        created() {
            this.getListByRest()
            // this.addBook()
            // this.addSerie()
            // this.addBookWithSerie()
        },
        methods: {
          getOtherPage(page) {
            // for graphQl
            // @todo find a way to identify if we get the data in the store or if we need to ask for new data
            this.pagination.endCursor = this.getBooks.pageInfo.endCursor
            // for Rest
            this.getListByRest(page)
          },

          getListByRest(page = 1) {
            this.isLoading = true
            const pageInt = Number.parseInt(page)
            const uri = `/api/books?page=${pageInt}`
            fetch(uri, {credentials: "same-origin"})
             .then(res => {
               if ([500, 403, 401, ].find(code => code === res.status)) {
                 logout()

                 return
               }

               return res.json()
            })
            .then(res => {
              this.isLoading = false

              // prevent response analyse
              if (!res) {
                return
              }

              // store data
              if (undefined !== res['hydra:member']) {
                this.books = res['hydra:member']
              }

              // manage pagination
              if (undefined !== res['hydra:view']) {
                ['first', 'last', 'next', 'previous',].forEach(key => {
                  if (undefined !== res['hydra:view'][`hydra:${key}`]) {
                    this.pagination.uris[key] = res['hydra:view'][`hydra:${key}`]

                    if (key === 'last') {
                      const pageParam = res['hydra:view'][`hydra:${key}`].match(/page=\d*/)
                      if (pageParam) {
                        this.pagination.total = Number.parseInt(pageParam[0].replace('page=', ''))
                      }
                    }
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
              "clientMutationId": "0"
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
                console.log("Book created", store, arguments[1])
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
              console.error("Books.vue error catched by apollo client when trying to create a Book: ", error)
              //// We restore the initial user input
              //this.newTag = newTag
            })
          },

          addSerie() {
            const newSerie = {
                "name": "test serie",
                "clientMutationId": "1"
            }

            this.$apollo.mutate({
              // Query
              mutation: gql`mutation createSerie(
                                $book: createSerieInput!
                            ) {

                                createSerie(
                                    input: $serie
                                ) {
                                    name
                                    id
                                }
                        }`,
              // Parameters
              variables: {
                book: newSerie,
              },
              // Update the cache with the result
              // The query will be updated with the optimistic response
              // and then with the real result of the mutation
              update: (store, { data: { newSerie } }) => {
                console.log("Serie created", store, arguments[1])
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
              //    __typename: 'Serie',
              //    id: -1,
              //    label: book,
              //  },
              //},
            }).then((data) => {
              // Result
              console.log(data)
            }).catch((error) => {
              // Error
              console.error("Books.vue error catched by apollo client when trying to create a Serie: ", error)
              //// We restore the initial user input
              //this.newTag = newTag
            })
          },

          addBookWithSerie() {
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
                console.log("Book with Serie created", store, arguments[1])
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
              console.error("Books.vue error catched by apollo client when trying to create a Book with a Serie: ", error)
              //// We restore the initial user input
              //this.newTag = newTag
            })
          }
        },
        apollo: {
          // @todo externalize all this in mixins to be able to re-use it !
          getBooks: {
            query: gql`query getBooksQry($first: Int, $after: String ) {
    getBooks: books(first: $first, after: $after ) {
      edges {
        node {
          id
          title
        }
      }
      pageInfo {
        endCursor
      }
    }
  }`,
            variables() {
              return {
                first: this.pagination.itemPerPage,
                after: this.pagination.endCursor
              }
            },
            // Additional options here
            fetchPolicy: 'cache-and-network',
        },
        }
    };
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
