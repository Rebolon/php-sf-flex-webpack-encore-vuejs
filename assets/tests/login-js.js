import { Selector, ClientFunction } from 'testcafe'
import { StandardVueJSAccUser } from './tools/authentification'
import { jsLoginFormPath, jsLoginSuccessPath } from './tools/uris'

import { Debugger } from './tools/debug'

console.log('debug', jsLoginFormPath)

fixture `Test vuejs login`
    .page `http://localhost:${process.env.npm_package_config_server_port_web}/${jsLoginFormPath}`

test('Login page', async t => {
  Debugger()
  const getLocation = ClientFunction(() => document.location.href)
  const head = await Selector('head')
  const form = await Selector('form.login')
  const toast = await Selector('div.q-toast-container')
  await t
  .navigateTo('/demo/form#/todos')
  .expect(getLocation()).notContains('#/todos')
  .expect(form.find('input[name="username"]').count).eql(1, "Should find 1 input for username")
  .expect(form.find('input[name="username"]').count).eql(1, "Should find 1 input for password")
  .expect(head.find('meta[name="csrf_token"]').count).eql(1, "Should find 1 meta for csrf protection")
  .typeText('input[name="username"]', 'fakeUser')
  .typeText('input[name="password"]', 'fakeUser11111')
  .click('button')
  .expect(getLocation()).contains(jsLoginFormPath)
  .expect(toast.find('div.q-toast.bg-negative').count).eql(1, "Missing warning toast message")

  .click('input[name="username"]')
  .pressKey('ctrl+a delete')

  .useRole(StandardVueJSAccUser)
  .navigateTo(jsLoginSuccessPath)
  .expect(getLocation()).contains(jsLoginSuccessPath)
})
