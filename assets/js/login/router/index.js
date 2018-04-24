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

export default new Router({
    routes: [
        {
            path: '/',
            name: 'Login',
            component: Login,
            props: {
                default: true,
                redirect: '/secured',
                loginUri: loginInfos.uriJson
            },
        },
        {
            path: '/secured',
            name: 'Secured',
            component: Secured,
            props: true,
            beforeEnter: vueRouterIsLoggedIn,
        },
    ],
})
