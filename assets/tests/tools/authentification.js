import { Role } from 'testcafe'
import { phpLoginFormPath } from './uris'
import { jsLoginFormPath } from './uris'
import { host } from '../../js/lib/config'

const usernameStd = 'test'
const passwordStd = 'test'

/**
 * more information on how to use Role here : http://devexpress.github.io/testcafe/documentation/test-api/authentication/user-roles.html
 *
 * @type {Role}
 */
export const StandardSfAccUser = Role(`http://${host}${phpLoginFormPath}`, async t => {
  await t
      .typeText('#username', usernameStd)
      .typeText('#password', passwordStd)
      .click('button[type="submit"]')
}, {
    preserveUrl: true
})

export const StandardVueJSAccUser = Role(`http://${host}${jsLoginFormPath}`, async t => {
  await t
      .typeText('input[name="username"]', usernameStd)
      .typeText('input[name="password"]', passwordStd)
      .click('button')
}, {
  preserveUrl: true
})
