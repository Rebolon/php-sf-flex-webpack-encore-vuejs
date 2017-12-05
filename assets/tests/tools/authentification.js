import { Role } from 'testcafe'
import { phpLoginFormPath } from './uris'
import { jsLoginFormPath } from './uris'

const usernameStd = 'test'
const passwordStd = 'test'

/**
 * more information on how to use Role here : http://devexpress.github.io/testcafe/documentation/test-api/authentication/user-roles.html
 *
 * @type {Role}
 */
export const StandardSfAccUser = Role(`http://localhost:${process.env.npm_package_config_server_port_web}/${phpLoginFormPath}`, async t => {
  await t
  .typeText('#username', usernameStd)
  .typeText('#password', passwordStd)
  .click('button[type="submit"]')
})

export const StandardVueJSAccUser = Role(`http://localhost:${process.env.npm_package_config_server_port_web}/${jsLoginFormPath}`, async t => {
  await t
  .typeText('input[name="username"]', usernameStd)
  .typeText('input[name="password"]', passwordStd)
  .click('button')
})
