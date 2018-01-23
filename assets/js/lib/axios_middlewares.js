import axios from 'axios'
import { logout } from './login'
import { getTokenFromMeta } from './csrf_token'
import { csrf_parameter } from './config'

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

export const csrfInterceptors = axios.interceptors.request.use(function (config) {
    let meta = getTokenFromMeta()

    switch (config.method.toLowerCase()) {
        case 'get':
            if (!config.params) {
                config.params = {}
            }
            config.params[csrf_parameter] = meta
            break;
        case 'post': // and all other methods throught default
        default:
            config.data[csrf_parameter] = meta
    }

    console.info('axios intercep request', 'csrf')

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

export const csrfResponseInterceptors = axios.interceptors.response.use(function (response) {
    console.info('axios intercep response', 'csrf')

    return response
}, function (error) {
    console.info('axios intercep response error', 'csrf')
    // @todo Is it possible to do a retry of the request with a new token ?

    let status = error.response.status
    if (error.response.data && error.response.data.code) {
        status = error.response.data.code
    }

    if (status === 423) {
        getToken().then(res => Toast.create.warning(`Invalid token, please try again`))
    }

    return Promise.reject(error)
})

export default axios
