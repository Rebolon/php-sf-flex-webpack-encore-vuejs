import { AUTH_LOGIN, AUTH_LOGOUT, AUTH_ERROR, AUTH_CHECK } from 'react-admin'
import { host, loginInfos,  } from '../../lib/config'
import getToken from '../../lib/csrfToken'

// Change this to be your own login check route.
const authenticationTokenUri = `//${host}${loginInfos.uriLogin.jwt}`

let csrf_token = ''

// get a csrf token and then do the auth
const initToken = () => {
    getToken()
        .then(response => csrf_token = response)
        .catch(err => console.warn('initToken', 'getToken', err))
}

initToken()

// @todo mutualize request params coz it's also used in other components in js/login/Login.vue
export default (type, params) => {
    switch (type) {
        case AUTH_LOGIN:
            const { username, password } = params;
            const body = {}
            body[loginInfos.loginUsernamePath] = username
            body[loginInfos.loginPasswordPath] = password

            const request = new Request(authenticationTokenUri, {
                method: 'POST',
                body: JSON.stringify(body),
                headers: new Headers({ 'Content-Type': 'application/json' }),
            });

            return fetch(request)
                .then(response => {
                    if (response.status < 200 || response.status >= 300) throw new Error(response.statusText);

                    return response.json();
                })
                .then(({ token }) => {
                    localStorage.setItem('token', token); // The token is stored in the browser's local storage
                    window.location.replace('/admin'); // @todo should be in config or somewhere-else
                });

        case AUTH_LOGOUT:
            localStorage.removeItem('token');
            break;

        case AUTH_ERROR:
            if (401 === params.status || 403 === params.status) {
                localStorage.removeItem('token');

                return Promise.reject();
            }
            break;

        case AUTH_CHECK:
            return localStorage.getItem('token') ? Promise.resolve() : Promise.reject();

        default:
            return Promise.resolve();
    }
}
