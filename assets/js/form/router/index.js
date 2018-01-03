import Vue from 'vue'
import Router from 'vue-router'
import Login from '../components/Login.vue'
import Books from '../components/Books.vue'

Vue.use(Router)

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
