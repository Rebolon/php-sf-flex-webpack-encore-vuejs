import { Selector } from 'testcafe'
import { host, port } from '../js/lib/config'
import {scheme} from "./tools/authentification";

fixture `Test homepage`
    .page `${scheme}://${host}`

test('Test homepage', async t => {
    const expectedCount = 15
    const h1 = await Selector('h2')
    const list = await Selector('ul li')

    await t
        .expect(h1.innerText).eql("List of demos", "Title not found")
        .expect(list.count).eql(expectedCount, `Should find ${expectedCount} items in list`)
})
