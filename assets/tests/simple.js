import { Selector } from 'testcafe';

fixture.only `Test`
    .page `http://localhost/`;

test('Test 1', async t => {
    await t
        .click(Selector('.list-group-item').find('a').withText('Basic'))
    ;
});