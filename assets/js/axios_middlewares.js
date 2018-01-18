import axios from 'axios'
import { logout } from './login'

// @todo add an interceptors that will always retrieve the csrf token and add it inside the request
// make sure that api-platform is also compatible with csrf token and implement it

export const noCacheInterceptors = axios.interceptors.request.use(function (config) {
    const headers = {
        'Cache-Control': 'no-cache',
    }

    config.headers = Object.assign(config.headers ? config.headers : {}, headers)

    console.info('axios intercep request', 'noCache')

    return config
}, function (error) {
    // Do something with request error
    return Promise.reject(error)
})

export const jsonInterceptors = axios.interceptors.request.use(function (config) {
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }

    config.headers = Object.assign(config.headers ? config.headers : {}, headers)

    console.info('axios intercep request', 'json')

    return config
}, function (error) {
    // Do something with request error
    return Promise.reject(error)
})

export const withCredentialsInterceptors = axios.interceptors.request.use(function (config) {
    config.withCredentials = true
    console.info('axios intercep request', 'credentials')

    return config
}, function (error) {
    // Do something with request error
    return Promise.reject(error)
})

export const logoutInterceptors = axios.interceptors.request.use(function (config) {
    config.validateStatus = function (status) {
        // @todo remove 500 when evereything fixed with Sf & json_login
        if ([500, 420, 403, 401].find(code => code === status)) {
            console.table([{
                uri: config.url,
                status,
            }])

            logout()

            return false
        }

        return true
    }
    console.info('axios intercep request', 'logout')

    return config
}, function (error) {
    // Do something with request error
    return Promise.reject(error)
})

export default axios
