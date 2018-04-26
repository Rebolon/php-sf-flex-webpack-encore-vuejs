import router from '../form-quasar-vuejs/router/index'
import { Notify } from 'quasar-framework/dist/quasar.mat.esm'
import axios from 'axios'
import { logoutStdInterceptors } from './axiosMiddlewares'
import Rx from 'rxjs/Rx'
import { loginInfos } from './config'

// allow components to be alerted when the user is logged in / off
const IsLoggedInSubject = new Rx.ReplaySubject(2)
export const IsLoggedInObservable = IsLoggedInSubject.asObservable().filter(data => Boolean(data && data.isLoggedIn))

axios.interceptors.request.eject(logoutStdInterceptors)

// @todo move it to RxJs implementation with subscribe + only call the uri on last call of the method during 300ms
export default function isLoggedIn(loaderToActivate, uri = loginInfos.uriIsLoggedIn.json) {
    return new Promise((resolve, reject) => {
        if (loaderToActivate && loaderToActivate.isLoading) {
            loaderToActivate.isLoading = true
        }

        axios.get(uri)
            .then(res => {
                IsLoggedInSubject.next(res.data)
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
    IsLoggedInSubject.next(undefined)
}

export const logout = function() {
    resetLoginInfo()
    Notify.create({
        message: 'You have been logged out.',
        type: 'info'
    })

    // @todo check if current route is !== from login then go to login else do nothing
    console.info('compare the location.href with / if different then push / to router, check that you dont loop infinitly')
    if (location.hash.replace('#/', '/') !== '/') {
        router.push('/')
    }
}
