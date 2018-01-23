// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App.vue'
import router from './router'
import Quasar from 'quasar-framework'
import Vuelidate from 'vuelidate'
import VueApollo from 'vue-apollo'
import { apolloProvider } from '../lib/apollo'

require('../../../node_modules/quasar-framework/dist/quasar.mat.css')
// not sure that fonts are loaded finely
import 'quasar-extras/roboto-font'
import 'quasar-extras/material-icons'
import 'quasar-extras/fontawesome'
// import 'quasar-extras/ionicons'
// import 'quasar-extras/animate'

// @todo make it dynamic using npm scripts and fetching .env file
Vue.config.productionTip = false

Vue.use(Quasar)
Vue.use(Vuelidate)
Vue.use(VueApollo)

Quasar.start(() => {
    /* eslint-disable no-new */
    new Vue({
        el: '#app',
        router,
        apolloProvider,
        render: h => h(App),
    })
})
