// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App.vue'
import router from './router'
import { isProduction } from '../lib/config'
import Vuelidate from 'vuelidate'
import VueApollo from 'vue-apollo'
import { apolloProvider } from '../lib/apollo'

import 'devextreme/dist/css/dx.common.css';
import 'devextreme/dist/css/dx.light.compact.css';

Vue.config.productionTip = isProduction()

Vue.use(Vuelidate)
Vue.use(VueApollo)

/* eslint-disable no-new */
new Vue({
    el: '#app',
    router,
    apolloProvider,
    render: h => h(App),
})
