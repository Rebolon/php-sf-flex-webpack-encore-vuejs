export const BooksTableDefinition = [
    {
        label: 'Id',
        field: 'id',
        width: '25px',
        filter: false,
        sort: false,
        type: 'number',
    },
    {
        label: 'Title',
        field: 'title',
        width: '150px',
        filter: true,
        sort: true,
        type: 'string',
    },
    {
        label: 'Description',
        field: 'description',
        width: '150px',
        filter: true,
        sort: true,
        type: 'string',
    },
    {
        label: 'Index in serie',
        field: 'indexInSerie',
        width: '50px',
        filter: false,
        sort: false,
        type: 'number',
        format(value, row) {
            if (undefined === row) {
                return 0
            }

            return row.indexInSerie ? row.indexInSerie : ""
        }
    },
    {
        label: 'Reviews',
        field: 'reviews',
        width: '50px',
        filter: false,
        sort: true,
        type: 'number',
        format(value, row) {
            if (undefined === row) {
                return 0
            }

            return row.reviews.length
        }
    },
    {
        label: 'Authors',
        field: 'authors',
        width: '50px',
        filter: false,
        sort: true,
        type: 'number',
        format(value, row) {
            if (undefined === row) {
                return 0
            }

            return row.authors.length
        }
    },
    {
        label: 'Editors',
        field: 'editors',
        width: '50px',
        filter: false,
        sort: true,
        type: 'number',
        format(value, row) {
            if (undefined === row) {
                return 0
            }

            return row.editors.length
        }
    }
]
