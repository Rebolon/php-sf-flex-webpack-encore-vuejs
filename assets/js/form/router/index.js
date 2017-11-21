import Vue from 'vue';
import Router from 'vue-router';
import Login from '../components/Login.vue';
import Todos from '../components/Todos.vue';

Vue.use(Router);

const isLoggedIn = (to, from, next) => {
    // do check on cookie/token and let it go if ok, or redirect to login
    next('/login')
}

export default new Router({
    routes: [
        {
            path: '/login',
            name: 'Login',
            component: Login,
            props: true,
        },
        {
            path: '/',
            name: 'Todos',
            component: Todos,
            props: true,
            beforeEnter: isLoggedIn,
        },
    ],
});
