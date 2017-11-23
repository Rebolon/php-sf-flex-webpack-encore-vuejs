import { Selector, ClientFunction } from 'testcafe'

fixture `Test homepage`
    .page `http://localhost:${process.env.npm_package_config_server_port_web}`

test('List of demos', async t => {
    const h1 = await Selector('h1')
    const list = await Selector('ul li')

    await t
        .expect(h1.innerText).eql("List of demos", "Title not found")
        .expect(list.count).eql(5, "Should find 5 items in list")
})
