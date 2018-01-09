import Vue from 'vue'
import Router from 'vue-router'
import Login from '../components/Login.vue'
import Books from '../components/Books.vue'

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
const isLoggedIn = (to, from, next) => {
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
            props: true,
        },
        {
            path: '/books',
            name: 'Books',
            component: Books,
            props: true,
            beforeEnter: isLoggedIn,
        },
    ],
})
