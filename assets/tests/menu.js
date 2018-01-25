import { Selector } from 'testcafe'
import { host, port } from '../js/lib/config'

fixture `Test homepage`
    .page `http://${host}`

test('List of demos', async t => {
    const expectedCount = 14
    const h1 = await Selector('h2')
    const list = await Selector('ul li')

    await t
        .expect(h1.innerText).eql("List of demos", "Title not found")
        .expect(list.count).eql(expectedCount, `Should find ${expectedCount} items in list`)
})
