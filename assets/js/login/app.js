// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App.vue'
import router from './router'
import { isProduction } from '../lib/config'
import Vuelidate from 'vuelidate'

import Quasar, { Notify } from 'quasar-framework/dist/quasar.mat.esm'
import 'quasar-framework/dist/umd/quasar.mat.css'
import 'quasar-extras/roboto-font'
import 'quasar-extras/material-icons'
import 'quasar-extras/fontawesome'

Vue.config.productionTip = isProduction()

Vue.use(Quasar, {
    plugins: [Notify]
})
Vue.use(Vuelidate)

/* eslint-disable no-new */
new Vue({
    el: '#app',
    router,
    render: h => h(App),
})
