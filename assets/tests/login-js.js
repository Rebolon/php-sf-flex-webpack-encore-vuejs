import { Selector, ClientFunction } from 'testcafe'
import {StandardVueJSAccUser, usernameStd, passwordStd, scheme} from './tools/authentification'
import { jsLoginFormPath, jsLoginSuccessPath } from './tools/uris'
import { host, csrfParameter } from '../js/lib/config'

const booksList = Selector('div.books')
const head = Selector('head')
const form = Selector('form.login')
const toast = Selector('div.q-notifications')
const getLocation = ClientFunction(() => document.location.href)

// to debug in testcafe, use .debug() on t variable, or add --debug-mode in the running script
// to focus on this test use 'only'
//fixture.only `Test vuejs login`
fixture `Test vuejs login`
    .page `${scheme}://${host}${jsLoginFormPath}`

test('Login page: vuejs login', async t => {
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
      .expect(toast.find('div.q-alert.bg-warning').count).eql(1, "Missing warning toast message")

      .useRole(StandardVueJSAccUser)
      //.debug() //this is where firefox seems to crash, but not sure
      .wait(3000)
      .expect(getLocation()).contains(jsLoginSuccessPath)
      .expect(booksList.exists).ok()
})
