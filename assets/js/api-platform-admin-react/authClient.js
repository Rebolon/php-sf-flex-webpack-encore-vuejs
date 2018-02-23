import { AUTH_LOGIN, AUTH_LOGOUT, AUTH_ERROR, AUTH_CHECK } from 'admin-on-rest'
import { host } from '../lib/config'
import { axios } from 'axios'
import getToken from '../lib/csrfToken'

// Change this to be your own login check route.
const login_uri = `//${host}/demo/login/json`

let csrf_token = ''

// get a csrf token and then do the auth
export const initToken = () => {
    getToken()
        .then(response => csrf_token = response)
        .catch(err => console.warn('initToken', 'getToken', err))
}

// @todo mutualize request params coz it's also used in other components in js/login/Login.vue
export const authClient = (type, params) => {
    switch (type) {
        case AUTH_LOGIN:
            const { username, password } = params
            const request = new Request(`${login_uri}`, {
                method: 'POST',
                body: JSON.stringify({ login_username: username, login_password: password, _csrf_token: csrf_token }),
                headers: new Headers({ 'Content-Type': 'application/json' }),
                credentials: "include"
            })

            return fetch(request)
            .then(response => {
                if (response.status < 200 || response.status >= 300) throw new Error(response.statusText)

                return response.json()
            })
            .then(({ token }) => {
                localStorage.setItem('token', token) // The JWT token is stored in the browser's local storage
            })
            .catch(err => {
                console.warn('authClient', 'getToken', err)
            })
        case AUTH_LOGOUT:
            localStorage.removeItem('token')
            break

        case AUTH_ERROR:
            if (401 === params.status || 403 === params.status) {
                localStorage.removeItem('token')

                return Promise.reject()
            }
            break

        case AUTH_CHECK:
            return localStorage.getItem('token') ? Promise.resolve() : Promise.reject()

        default:
            return Promise.resolve()
    }
}
