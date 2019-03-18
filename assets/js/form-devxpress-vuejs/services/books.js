import CustomStore from 'devextreme/data/custom_store';
import { axiosJsonLd } from '../../lib/axiosMiddlewares'
import { apiPlatformPrefix, apiConfig, host } from '../../lib/config'

const pagination = {
    endCursor: '',
    nexEndCursor: '',

    // for quasar
    sortBy: null, // String, column "name" property value
    descending: false,
    page: 1,
    rowsPerPage: apiConfig.itemsPerPage, // current rows per page being displayed
    rowsNumber: 0 // mandatory for server-side pagination
}

// dataStore for GraphQL Queries: vue-apollo will inject data in vue components based on the name of the query

const getBooks = {
    pageInfo: {},
}

const uri = `//${host}${apiPlatformPrefix}/books`

export const dataSource = {
    store: new CustomStore({
        load: function(loadOptions) {
            let params = '?';

            // @todo not sure it's a good algo: skip maybe the number of rows wheras apiPlatform expect a pageNumber
            // const nextPage = loadOptions.skip > 0 ? (loadOptions.skip/loadOptions.take)+1 : 1
            params += `page=${loadOptions.skip || 1}`;

            if (loadOptions.take || loadOptions.itemsPerPage) {
                params += `&${apiConfig.itemsPerPageParameterName}=${loadOptions.take || loadOptions.itemsPerPage}`;
            }

            if(loadOptions.sort) {
                params += `&orderby=${loadOptions.sort[0].selector}`;
                if(loadOptions.sort[0].desc) {
                    params += ' desc';
                }
            }

            return axiosJsonLd
                .get(`${uri}${params}`)
                .then((response) => {
                    const data = response.data

                    return {
                        data: data['hydra:member'],
                        totalCount: data['hydra:totalItems'],
                    };
                })
                .catch(() => { throw 'Data Loading Error'; });
        }
    })
};

export default {
    getOtherPage({pageInfos, filter}) {
        // for graphQl
        // @todo find a way to identify if we get the data in the store or if we need to ask for new data
        // @todo how to manage 'previous' link ? for instance this code should not work in all case, only if user click on next
        if (getBooks.pageInfo.hasNextPage) {
            const [last] = [...getBooks.edges].reverse()
            pagination.endCursor = last.cursor
        }

        // change # of rows returned by the grid
        if (pageInfos.rowsPerPage) {
            pagination.rowsPerPage = pageInfos.rowsPerPage
        }

        // for Rest
        this.getListByRest(pageInfos.page)
    },

    getListByRest(page = 1) {
        const pageInt = Number.parseInt(page)
        let uri = `${apiPlatformPrefix}/books?page=${pageInt}`

        if (pagination.rowsPerPage !== apiConfig.rowsPerPage) {
            uri += `&${apiConfig.itemsPerPageParameterName}=${pagination.rowsPerPage}`
        }

        axiosJsonLd.get(uri)
            .then(res => {
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
                    pagination.rowsNumber = content['hydra:totalItems']
                }

                if (undefined !== content['hydra:view']) {
                    ['first', 'last', 'next', 'previous'].forEach(key => {
                        if (undefined !== content['hydra:view'][`hydra:${key}`]) {
                            const pageParam = content['hydra:view'][`hydra:${key}`].match(/page=\d*/)
                            const pageValue = Number.parseInt(pageParam[0].replace('page=', ''))
                            if (key === 'next'
                                && pageValue !== pagination.page) {
                                pagination.page += 1
                            }
                        }
                    })
                }
            })
    }
}
