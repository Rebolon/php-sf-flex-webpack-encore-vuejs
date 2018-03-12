import React from 'react'; // Do not remove this import or DevXpress will fail
import {
    Grid, Table, TableHeaderRow
} from '@devexpress/dx-react-grid-bootstrap4' /*or  '@devexpress/dx-react-grid-material-ui' */

export default () => (
    <Grid
        rows={[
                { id: 0, product: 'DevExtreme', owner: 'DevExpress' },
        { id: 1, product: 'DevExtreme Reactive', owner: 'DevExpress' },
        ]}
        columns={[
                { name: 'id', title: 'ID' },
        { name: 'product', title: 'Product' },
        { name: 'owner', title: 'Owner' },
        ]}>
        <Table />
        <TableHeaderRow />
    </Grid>
)
