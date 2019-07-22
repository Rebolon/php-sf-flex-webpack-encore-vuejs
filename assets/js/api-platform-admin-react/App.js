import React from 'react'
import parseHydraDocumentation from '@api-platform/api-doc-parser/lib/hydra/parseHydraDocumentation';
import { HydraAdmin, hydraClient, fetchHydra as baseFetchHydra } from '@api-platform/admin'
import { authProvider, initToken } from './authProvider'
import { host, apiPlatformPrefix } from '../lib/config'
import { Route, Redirect } from 'react-router-dom';
import './index.css';
import registerServiceWorker from './registerServiceWorker';

initToken();

const entrypoint = `//${host}${apiPlatformPrefix}`
const fetchHeaders = (options) => {
    const token = localStorage.getItem('token');

    if (!token) {
        return
    }

    options.headers.set('Authorization', `${token}`);
};
const fetchHydra = (url, options = {}) => {
    if (!options.headers) options.headers = new Headers({ Accept: 'application/ld+json' });
    fetchHeaders(options)

    // fix https://github.com/api-platform/api-platform/issues/584
    if (apiPlatformPrefix) {
        url = url.replace(`${apiPlatformPrefix}${apiPlatformPrefix}/`, `${apiPlatformPrefix}/`)
    }

    return baseFetchHydra(url, options);
};

const dataProvider = api => hydraClient(api, fetchHydra);
const apiDocumentationParser = entrypoint =>
    parseHydraDocumentation(entrypoint, {
        headers: new Headers(fetchHeaders),
    }).then(
        ({ api }) => ({ api }),
        result => {
            debugger
            const { api, status } = result;

            if (status === 401) {
                return Promise.resolve({
                    api,
                    status,
                    customRoutes: [
                        <Route path="/" render={() => <Redirect to="/login" />} />,
                    ],
                });
            }

            return Promise.reject(result);
        }
    );

ReactDOM.render(
    <HydraAdmin
        apiDocumentationParser={apiDocumentationParser}
        authProvider={authProvider}
        entrypoint={entrypoint}
        dataProvider={dataProvider}
    />, document.getElementById('root'));

registerServiceWorker();
