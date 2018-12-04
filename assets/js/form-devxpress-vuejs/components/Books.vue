<template>
    <div class="books">
        <label class="title">
            <h5 class="title">List of books</h5>
        </label>

        <!--q-table
                :data="books"
                :columns="dataTableColumns"
                :pagination.sync="pagination"
                :loading="isLoading"
                @request="getOtherPage($event)"
                @refresh=""
        >
        </q-table-->

        <dx-data-grid :data-source="getListByRest">
            <dx-column data-field="id"/>
            <dx-column data-field="title" caption="Title"/>
        </dx-data-grid>

    </div>
</template>

<script>
import DxDataGrid, { DxColumn } from "devextreme-vue/data-grid";

import gql from 'graphql-tag'

export default {
    name: 'Books',
    components: {
        DxDataGrid,
        DxColumn,
    },
    data() {
        return {
            msg: 'List of books',
            isLoading: true,
            books: [],
            id: undefined,
        }
    },
    created() {
        this.getListByRest()
    },
    methods: {

        addBook() {
            const newBook = {
                title: 'test livre',
                description: 'test livre',
                indexInSerie: 1,
                clientMutationId: '0',
            }

            this.$apollo
                .mutate({
                    // Query
                    mutation: gql`
                        mutation createBook($book: createBookInput!) {
                            createBook(input: $book) {
                                title
                                id
                                serie {
                                    name
                                    id
                                }
                            }
                        }
                    `,
                    // Parameters
                    variables: {
                        book: newBook,
                    },
                    // Update the cache with the result
                    // The query will be updated with the optimistic response
                    // and then with the real result of the mutation
                    update: (store, { data: { newBook } }) => {
                        console.log('Book created', store, arguments[1])
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
                })
                .then(data => {
                    // Result
                    console.log(data)
                })
                .catch(error => {
                    // Error
                    console.error('Books.vue error catched by apollo client when trying to create a Book: ', error)
                    //// We restore the initial user input
                    //this.newTag = newTag
                })
        },

        addSerie() {
            const newSerie = {
                name: 'test serie',
                clientMutationId: '1',
            }

            this.$apollo
                .mutate({
                    // Query
                    mutation: gql`
                        mutation createSerie($book: createSerieInput!) {
                            createSerie(input: $serie) {
                                name
                                id
                            }
                        }
                    `,
                    // Parameters
                    variables: {
                        book: newSerie,
                    },
                    // Update the cache with the result
                    // The query will be updated with the optimistic response
                    // and then with the real result of the mutation
                    update: (store, { data: { newSerie } }) => {
                        console.log('Serie created', store, arguments[1])
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
                })
                .then(data => {
                    // Result
                    console.log(data)
                })
                .catch(error => {
                    // Error
                    console.error('Books.vue error catched by apollo client when trying to create a Serie: ', error)
                    //// We restore the initial user input
                    //this.newTag = newTag
                })
        },

        addBookWithSerie() {
            const newBook = {
                title: 'test livre',
                description: 'test livre',
                indexInSerie: 1,
                clientMutationId: '0',
                serie: {
                    name: 'test serie',
                    clientMutationId: '1',
                },
            }

            this.$apollo
                .mutate({
                    // Query
                    mutation: gql`
                        mutation createBook($book: createBookInput!) {
                            createBook(input: $book) {
                                title
                                id
                                serie {
                                    name
                                    id
                                }
                            }
                        }
                    `,
                    // Parameters
                    variables: {
                        book: newBook,
                    },
                    // Update the cache with the result
                    // The query will be updated with the optimistic response
                    // and then with the real result of the mutation
                    update: (store, { data: { newBook } }) => {
                        console.log('Book with Serie created', store, arguments[1])
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
                })
                .then(data => {
                    // Result
                    console.log(data)
                })
                .catch(error => {
                    // Error
                    console.error(
                        'Books.vue error catched by apollo client when trying to create a Book with a Serie: ',
                        error,
                    )
                    //// We restore the initial user input
                    //this.newTag = newTag
                })
        },
    },
    apollo: {
        // @todo externalize all this in mixins to be able to re-use it !
        getBooks: {
            query: gql`
                query getBooksQry($first: Int, $after: String) {
                    getBooks: books(first: $first, after: $after) {
                        edges {
                            node {
                                id
                                title
                            }
                            cursor
                        }
                        pageInfo {
                            endCursor
                            hasNextPage
                        }
                    }
                }
            `,
            variables() {
                return {
                    first: this.pagination.rowsPerPage,
                    after: this.pagination.endCursor,
                }
            },
            // Additional options here
            fetchPolicy: 'cache-and-network',
        },
        // @sample on how to do 2 queries in one call
        getBooksAndSerie: {
            // getBooksAndSerie is mandatory on response if we don't want to get a console.error
            query: gql`
                query getBooksAndSerieQry($firstBook: Int, $afterBook: String, $firstSerie: Int, $afterSerie: String) {
                    getBooksAndSerie: books(first: $firstBook, after: $afterBook) {
                        edges {
                            node {
                                id
                                title
                            }
                            cursor
                        }
                        pageInfo {
                            endCursor
                            hasNextPage
                        }
                    }
                    getSeries: series(first:$firstSerie, after: $afterSerie) {
                        edges {
                            node {
                                name
                            }
                            cursor
                        }
                        pageInfo {
                            endCursor
                            hasNextPage
                        }
                    }
                }
            `,
            result({data}) {
                let result = {
                    books: {},
                    series: {}
                }

                if (data) {
                    if (data.getBooksAndSerie) {
                        result.books = data.getBooksAndSerie
                    }

                    if (data.getSeries) {
                        result.series = data.getSeries
                    }
                }

                this.getBooksAndSerie = result
            },
            variables() {
                return {
                    firstBook: this.pagination.rowsPerPage,
                    afterBook: this.pagination.endCursor,
                    firstSerie: this.pagination.rowsPerPage,
                }
            },
            // Additional options here
            fetchPolicy: 'cache-and-network',
        },
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
