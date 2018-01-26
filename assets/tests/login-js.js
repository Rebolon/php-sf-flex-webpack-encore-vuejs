import { Selector, ClientFunction } from 'testcafe'
import { StandardVueJSAccUser } from './tools/authentification'
import { jsLoginFormPath, jsLoginSuccessPath } from './tools/uris'
import { host, csrfParameter } from '../js/lib/config'

fixture `Test vuejs login`
    .page `http://${host}${jsLoginFormPath}`

test('Login page', async t => {
  const getLocation = ClientFunction(() => document.location.href)
  const head = await Selector('head')
  const form = await Selector('form.login')
  const toast = await Selector('div.q-toast-container')
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

  .useRole(StandardVueJSAccUser)
  // works with it but useless
  //.navigateTo(jsLoginSuccessPath)
  //.wait(3000)
  .expect(getLocation()).contains(jsLoginSuccessPath)
})
