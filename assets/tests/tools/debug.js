import { ClientFunction } from 'testcafe'

/**
 * add a debugger tool : in your test() function you just have to add this :
 *    `await debug()`
 *    and the test runner will stop the script in the launched browser
 *
 * @type {ClientFunction}
 */
export const Debugger = ClientFunction(() => {
  debugger
})