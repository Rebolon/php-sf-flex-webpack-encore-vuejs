import Vue from 'vue'
import Router from 'vue-router'
import Login from '../components/Login.vue'
import Secured from '../components/Secured.vue'
import { loginInfos } from '../../lib/config'

Vue.use(Router)

/**
 * basic check on "is user logged in ?" but it could be interesting to mix this
 * with the ../../login.js -> isLoggedIn function that does a real http call to
 * do the check, maybe just do this each 10mn BUT WE COULD ALSO JUST RELY ON
 * fetch / apollo error check that must then do a go to login page
 *
 * @param to
 * @param from
 * @param next
 */
export const vueRouterIsLoggedIn = (to, from, next) => {
    // do check on cookie/token and let it go if ok, or redirect to login
    if (!localStorage.getItem('isLoggedIn')) {
        next(false)
    } else {
        next()
    }
}


/**
 * Just for the POC, i should need a login form dedicated for devxpress-angular app, but i don't have time for instance
 * so i add these options to allow redirect
 *
 * @type {string}
 */
// allow to use the Login component with dynamic redirect
let redirect = decodeURIComponent(
    location.search
        .substr(1)
        .split('&')
        .filter(item => item.split('=')[0] === 'redirect')
        .map(item => {
            if (!item) {
                return
            }

            return item.split('=')[1]
        })
        .join('')
)

if (!redirect) {
    redirect = '/secured'
}

let loggedInUri = loginInfos.uriIsLoggedIn.json
let loginUri = decodeURIComponent(
    // looks for jwt or json string in uri, it will determine which uri to login with (this is only for this demo app, you should not have to do this)
    location.pathname
        .split('/')
        .filter(item => ['json', 'jwt'].includes(item))
        .map(mode => {
            if (!mode) {
                return
            }

            switch (mode) {
                case 'jwt':
                    loggedInUri = loginInfos.uriIsLoggedIn.jwt

                    return loginInfos.uriLogin.jwt
                case 'json':
                default:
                    return loginInfos.uriLogin.json
            }
        })
        .join('')
)

if (!loginUri) {
    loginUri = loginInfos.uriLogin.json
}

export default new Router({
    routes: [
        {
            path: '/',
            name: 'Login',
            component: Login,
            props: {
                default: true,
                redirect: redirect,
                loginUri: loginUri,
                loggedInUri: loggedInUri,
            },
        },
        {
            path: '/secured',
            name: 'Secured',
            component: Secured,
            props: {
                default: true,
                loggedInUri: loggedInUri,
            },
            beforeEnter: vueRouterIsLoggedIn,
        },
    ],
})
