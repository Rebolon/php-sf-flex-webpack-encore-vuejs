import Vue from 'vue'
import Router from 'vue-router'
import Login from '../../login/components/Login.vue'
import Books from '../components/Books.vue'
import {vueRouterIsLoggedIn} from '../../login/router/index'

Vue.use(Router)

export default new Router({
    routes: [
        {
            path: '/',
            name: 'Login',
            component: Login,
            props: {
                default: true,
                redirect: '/books',
            },
        },
        {
            path: '/books',
            name: 'Books',
            component: Books,
            props: true,
            beforeEnter: vueRouterIsLoggedIn,
        },
    ],
})
