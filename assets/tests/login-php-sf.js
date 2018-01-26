import { Selector, ClientFunction } from 'testcafe'
import { StandardSfAccUser } from './tools/authentification'
import { phpLoginFormPath, phpLoginSuccessPath, phpLoginAuthenticatePath } from './tools/uris'
import { host, csrfParameter } from '../js/lib/config'

fixture `Test symfony login`
    .page `http://${host}${phpLoginFormPath}`

/**
 * Test the login page:
 *    * acces to secured page should fail
 *    * wrong user should stay on form and display alert message
 *    * right user should be able to access to a secured page
 */
test('Login page', async t => {
  const getLocation = ClientFunction(() => document.location.href)
  const form = await Selector(`form[action="${phpLoginAuthenticatePath}"]`)
  const alert = await Selector('div[role="alert"]')
  await t
  .navigateTo(phpLoginSuccessPath)
  .expect(getLocation()).contains(phpLoginFormPath, 'uri does not contain ' + phpLoginFormPath)
  .expect(form.find('#username').count).eql(1, "Should find 1 input for username")
  .expect(form.find('#password').count).eql(1, "Should find 1 input for password")
  .expect(form.find(`input[name="${csrfParameter}"]`).count).eql(1, "Should find 1 input for csrf protection")
  .typeText('#username', 'fakeUser')
  .typeText('#password', 'fakeUser11111')
  .click('button[type="submit"]')
  .expect(getLocation()).contains(phpLoginFormPath, 'uri does not contain ' + phpLoginFormPath)
  .expect(alert.innerText).eql('Invalid credentials.', "Missing warning message")

  .click("#username")
  .pressKey('ctrl+a delete')

  .useRole(StandardSfAccUser)
  .navigateTo(phpLoginSuccessPath) // i force navigation on this secured page coz it seems that if the Role works well, the current test does a redirection to current page
  .expect(getLocation()).contains(phpLoginSuccessPath, 'uri does not contain ' + phpLoginSuccessPath)
})
