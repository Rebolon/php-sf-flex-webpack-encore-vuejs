import Vue from 'vue';
import Router from 'vue-router';
import Login from '../components/Login.vue';
import Todos from '../components/Todos.vue';

Vue.use(Router);

const isLoggedIn = (to, from, next) => {
    // do check on cookie/token and let it go if ok, or redirect to login
    if (!localStorage.getItem('isLoggedIn')) {
        next('/login')
    } else {
        next(to)
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
            path: '/todos',
            name: 'Todos',
            component: Todos,
            props: true,
            beforeEnter: isLoggedIn,
        },
    ],
});