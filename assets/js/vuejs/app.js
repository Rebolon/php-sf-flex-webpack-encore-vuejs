// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App.vue'
import Loader from './components/Loader.vue'
import router from './router'

// @todo make it dynamic using npm scripts and fetching .env file
Vue.config.productionTip = false

Vue.component('Loader', Loader)

/* eslint-disable no-new */
new Vue({
    el: '#app',
    router,
    template: '<App/>',
    components: {App},
})
