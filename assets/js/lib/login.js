import router from '../form/router/index'
import { Toast } from 'quasar-framework'
import axios from 'axios'
import { logoutInterceptors } from './axiosMiddlewares'

axios.interceptors.request.eject(logoutInterceptors);

export default function isLoggedIn(loaderToActivate) {
    return new Promise((resolve, reject) => {
        if (loaderToActivate && loaderToActivate.isLoading) {
            loaderToActivate.isLoading = true
        }

        const uri = '/demo/login/json/isloggedin'
        axios.get(uri)
            .then(res => {
                localStorage.setItem('isLoggedIn', JSON.stringify(res.data))
                resolve(true)
            })
            .catch(err => {
                // @todo remove 500 when evereything fixed with Sf & json_login
                if ([500, 420, 403, 401].find(code => code === err.response.status)) {
                    console.info('login.js', err.response.status, 'will resetLoginInfo')
                    resetLoginInfo()
                }

                reject(err)
            })
            .finally(() => {
                if (loaderToActivate && loaderToActivate.isLoading) {
                    loaderToActivate.isLoading = false
                }
            })
    })
}

export const resetLoginInfo = function() {
    localStorage.removeItem('isLoggedIn')
}

export const logout = function() {
    resetLoginInfo()
    Toast.create.info('You have been logged out.')

    // @todo check if current route is !== from login then go to login else do nothing
    console.info('compare the location.href with / if different then push / to router, check that you dont loop infinitly')
    if (location.hash.replace('#/', '/') !== '/') {
        router.push('/')
    }
}
