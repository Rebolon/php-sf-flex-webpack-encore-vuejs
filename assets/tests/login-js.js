import { Selector, ClientFunction } from 'testcafe'
import { StandardVueJSAccUser } from './tools/authentification'
import { jsLoginFormPath, jsLoginSuccessPath } from './tools/uris'
import { host, csrfParameter } from '../js/lib/config'

const booksList = Selector('div.books')
const head = Selector('head')
const form = Selector('form.login')
const toast = Selector('div.q-toast-container')
const getLocation = ClientFunction(() => document.location.href)

fixture.only `Test vuejs login`
    .page `http://${host}${jsLoginFormPath}`

test('Login page: fake user', async t => {
  await t
      .navigateTo(jsLoginSuccessPath)
      .expect(getLocation()).notContains('#/books')
      .expect(form.find('input[name="username"]').count).eql(1, "Should find 1 input for username")
      .expect(form.find('input[name="username"]').count).eql(1, "Should find 1 input for password")
      .expect(head.find(`meta[name="${csrfParameter}"`).count).eql(1, "Should find 1 meta for csrf protection")
      .typeText('input[name="username"]', 'fakeUser')
      .typeText('input[name="password"]', 'fakeUser11111')
      .click('button')
      .expect(getLocation()).contains(jsLoginFormPath)
      .expect(toast.find('div.q-toast.bg-warning').count).eql(1, "Missing warning toast message")
})

// don't understand why, but sometimes it works (and the login form is well filled) and sometimes nothing happen !
// ugly behavior
test('Login page: normal user', async t => {
    await t
        //.useRole(StandardVueJSAccUser)
    // @todo until role doesn't work each time, i prefer to use those 3 lines of code
        .typeText('input[name="username"]', 'test')
        .typeText('input[name="password"]', 'test')
        .click('button')
        // works with it but useless
        //.navigateTo(jsLoginSuccessPath)
        .expect(getLocation()).contains(jsLoginSuccessPath)
        .expect(booksList.exists).ok()

})
