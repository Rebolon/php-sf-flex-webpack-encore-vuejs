<template>
    <div class="books">
        <label class="title">
            <h5 class="title">List of books</h5>
        </label>

        <q-table
                :data="books"
                :columns="dataTableColumns"
                :pagination.sync="pagination"
                :loading="isLoading"
                @request="getOtherPage($event)"
                @refresh=""
        >
        </q-table>

    </div>
</template>

<script>
import {
    QTable,
} from 'quasar-framework/dist/quasar.mat.esm'

import gql from 'graphql-tag'
import { axiosJsonLd } from '../../lib/axiosMiddlewares'
import { apiPlatformPrefix, apiConfig } from '../../lib/config'
import { BooksTableDefinition } from '../dataTableDefinitions/Books'

export default {
    name: 'Books',
    components: {
        QTable,
    },
    data() {
        return {
            msg: 'List of books',
            isLoading: true,
            books: [],
            dataTableColumns: BooksTableDefinition,
            id: undefined,
            pagination: {
                endCursor: '',
                nexEndCursor: '',

                // for quasar
                sortBy: null, // String, column "name" property value
                descending: false,
                page: 1,
                rowsPerPage: apiConfig.itemsPerPage, // current rows per page being displayed
                rowsNumber: 0 // mandatory for server-side pagination
            },

            // dataStore for GraphQL Queries: vue-apollo will inject data in vue components based on the name of the query
            getBooks: {
                pageInfo: {},
            },
        }
    },
    created() {
        this.getListByRest()
    },
    methods: {
        getOtherPage({ pagination, filter }) {
            // for graphQl
            // @todo find a way to identify if we get the data in the store or if we need to ask for new data
            // @todo how to manage 'previous' link ? for instance this code should not work in all case, only if user click on next
            if (this.getBooks.pageInfo.hasNextPage) {
                const [last] = [...this.getBooks.edges].reverse()
                this.pagination.endCursor = last.cursor
            }

            // change # of rows returned by the grid
            if (pagination.rowsPerPage) {
                this.pagination.rowsPerPage = pagination.rowsPerPage
            }

            // for Rest
            this.getListByRest(pagination.page)
        },

        getListByRest(page = 1) {
            this.isLoading = true
            const pageInt = Number.parseInt(page)
            let uri = `${apiPlatformPrefix}/books.jsonld?page=${pageInt}`

            if (this.pagination.rowsPerPage !== apiConfig.rowsPerPage) {
                uri += `&${apiConfig.itemsPerPageParameterName}=${this.pagination.rowsPerPage}`
            }

            axiosJsonLd.get(uri)
                .then(res => {
                    this.isLoading = false

                    // prevent response analyse
                    if (!res || !res.data) {
                        return
                    }

                    let content = res.data

                    // store data
                    if (undefined !== content['hydra:member']) {
                        this.books = content['hydra:member']
                    }

                    // manage pagination
                    if (undefined !== content['hydra:totalItems']) {
                        this.pagination.rowsNumber = content['hydra:totalItems']
                    }

                    if (undefined !== content['hydra:view']) {
                        ['first', 'last', 'next', 'previous'].forEach(key => {
                            if (undefined !== content['hydra:view'][`hydra:${key}`]) {
                                const pageParam = content['hydra:view'][`hydra:${key}`].match(/page=\d*/)
                                const pageValue = Number.parseInt(pageParam[0].replace('page=', ''))
                                if (key === 'next'
                                    && pageValue !== this.pagination.page) {
                                    this.pagination.page += 1
                                }
                            }
                        })
                    }
                })
        },

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
