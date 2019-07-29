import React from 'react';
import parseHydraDocumentation from '@api-platform/api-doc-parser/lib/hydra/parseHydraDocumentation';
import { HydraAdmin, hydraClient, fetchHydra as baseFetchHydra } from '@api-platform/admin';
import ReactDOM from 'react-dom';
import authProvider from './src/authProvider';
import { Route, Redirect } from 'react-router-dom';
import {MyLoginPage} from "./src/components/login";
import Api from '@api-platform/api-doc-parser/lib/Api';

const entrypoint = document.getElementById('api-entrypoint').innerText;
// original system with JWT
const fetchHeaders = {'Authorization': `Bearer ${localStorage.getItem('token')}`};
const fetchHydra = (url, options = {}) => baseFetchHydra(url, {
    ...options,
    headers: new Headers(fetchHeaders),
});
const dataProvider = api => hydraClient(api, fetchHydra);
const apiDocumentationParser = entrypoint =>
    parseHydraDocumentation(entrypoint, {
        headers: new Headers(fetchHeaders),
    }).then(
        ({ api }) => ({ api }),
        result => {
            let { api, status } = result;

            // hack to make it works
            if (!status) {
                status = 401
            }

            if (!api) {
                api = new Api(entrypoint, {resources: []})
            }
            // end of hack

            if (status === 401 || status === 403) {
                return Promise.resolve({
                    api,
                    status,
                    customRoutes: [
                        <Route path="/" render={() => <Redirect to="/login" />} />, // @todo should be in config or somewhere-else
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
        loginPage={MyLoginPage}
    />, document.getElementById('api-platform-admin'));
