import { Role } from 'testcafe'
import { phpLoginFormPath } from './uris'
import { jsLoginFormPath } from './uris'
import { host } from '../../js/lib/config'

export const scheme = 'http'
export const usernameStd = 'test_js'
export const passwordStd = 'test'

/**
 * more information on how to use Role here : http://devexpress.github.io/testcafe/documentation/test-api/authentication/user-roles.html
 *
 * @type {Role}
 */
export const StandardSfAccUser = Role(`${scheme}://${host}${phpLoginFormPath}`, async t => {
  await t
      .typeText('#username', usernameStd)
      .typeText('#password', passwordStd)
      .click('button[type="submit"]')
}, {
    preserveUrl: true
})

export const StandardVueJSAccUser = Role(`${scheme}://${host}${jsLoginFormPath}`, async t => {
  await t
      .typeText('input[name="username"]', usernameStd)
      .typeText('input[name="password"]', passwordStd)
      .click('button')
}, {
  preserveUrl: true
})
