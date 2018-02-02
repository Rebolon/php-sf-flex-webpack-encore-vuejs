import axios from 'axios'
import { logout } from './login'
import { getTokenFromMeta } from './csrfToken'
import { csrfParameter } from './config'
import { Toast } from 'quasar-framework'

// @todo add an interceptors that will always retrieve the csrf token and add it inside the request
// make sure that api-platform is also compatible with csrf token and implement it

export let axiosJson = axios.create({
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }
})

export let axiosJsonLd = axios.create({
    headers: {
        'Accept': 'application/ld+json',
        'Content-Type': 'application/ld+json',
    }
})

const RejectDoNothing = function (error) {
    // Do something with request error
    return Promise.reject(error)
}

const ResponseDoNothing = function (response) {
    return response
}

const NoCacheHeader = function (config) {
    const headers = {
        'Cache-Control': 'no-cache',
    }

    config.headers = Object.assign(config.headers ? config.headers : {}, headers)

    console.info('axios intercep request', 'noCache')

    return config
}

const WithCredentialsHeader = function (config) {
    config.withCredentials = true
    console.info('axios intercep request', 'credentials')

    return config
}

const CsrfTokenHeader = function (config) {
    let meta = getTokenFromMeta()

    switch (config.method.toLowerCase()) {
        case 'get':
            if (!config.params) {
                config.params = {}
            }
            config.params[csrfParameter] = meta
            break;
        case 'post': // and all other methods throught default
        default:
            config.data[csrfParameter] = meta
    }

    console.info('axios intercep request', 'csrf')

    return config
}

const CsrfTokenRetreiveOnInvalidResponse = function (error) {
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
}

const LogoutOnInvalidResponse = function (config) {
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
}

export const noCacheStdInterceptors = axios.interceptors.request.use(NoCacheHeader, RejectDoNothing)
export const noCacheJsonInterceptors = axiosJson.interceptors.request.use(NoCacheHeader, RejectDoNothing)
export const noCacheJsonLdInterceptors = axiosJsonLd.interceptors.request.use(NoCacheHeader, RejectDoNothing)

export const withCredentialsStdInterceptors = axios.interceptors.request.use(WithCredentialsHeader, RejectDoNothing)
export const withCredentialsJsonInterceptors = axiosJson.interceptors.request.use(WithCredentialsHeader, RejectDoNothing)
export const withCredentialsJsonLdInterceptors = axiosJsonLd.interceptors.request.use(WithCredentialsHeader, RejectDoNothing)

export const csrfStdInterceptors = axios.interceptors.request.use(CsrfTokenHeader, RejectDoNothing)
export const csrfJsonInterceptors = axiosJson.interceptors.request.use(CsrfTokenHeader, RejectDoNothing)
export const csrfJsonLdInterceptors = axiosJsonLd.interceptors.request.use(CsrfTokenHeader, RejectDoNothing)

export const logoutStdInterceptors = axios.interceptors.request.use(LogoutOnInvalidResponse, RejectDoNothing)
// @todo Should we logout with Api ?
export const logoutJsonInterceptors = axiosJson.interceptors.request.use(LogoutOnInvalidResponse, RejectDoNothing)
export const logoutJsonLdInterceptors = axiosJsonLd.interceptors.request.use(LogoutOnInvalidResponse, RejectDoNothing)

export const csrfResponseInterceptors = axios.interceptors.response.use(ResponseDoNothing, CsrfTokenRetreiveOnInvalidResponse)

export default axios
