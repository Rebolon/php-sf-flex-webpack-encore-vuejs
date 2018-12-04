import Vue from 'vue'
import Router from 'vue-router'
import Login from '../../login/components/Login.vue'
import Books from '../components/Books.vue'
import { vueRouterIsLoggedIn } from '../../login/router/index'
import { loginInfos } from '../../lib/config'

Vue.use(Router)

export default new Router({
    routes: [
        {
            path: '/',
            name: 'Books',
            component: Books,
            props: true,
            beforeEnter: vueRouterIsLoggedIn,
        },
    ],
})
