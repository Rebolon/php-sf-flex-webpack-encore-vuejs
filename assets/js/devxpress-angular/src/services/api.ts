import { Injectable } from '@angular/core'
import { HttpClient } from '@angular/common/http'
import { Observable } from 'rxjs/Observable'
import { environment } from '../environments/environment'

import 'rxjs/add/operator/do'
import 'rxjs/add/operator/map'

@Injectable()
export class ApiService {

    baseUrl = environment.rest.baseUrl

    options = {
        withCredentials: true,
        responseType: 'json',
        body: null,
        headers: {}
    }

    constructor(protected http: HttpClient) { }

    /**
     *
     * @param path
     * @returns {Observable<any>}
     */
    get(path: string, options: Object = {}): Observable<any> {
        const init = Object.assign(options, this.options)
        const uri = this.buildUri(path)

        return this.addJwtTokenIfExists()
            .http
            .request('GET', uri, init)
    }

    /**
     *
     * @param path
     * @param body
     * @returns {Observable<R>}
     */
    post(path: string, body: any, options: Object = {}): Observable<any> {
        const init = Object.assign(options, this.options)
        init.body = body
        const uri = this.buildUri(path)

        return this.addJwtTokenIfExists()
            .http
            .request('POST', uri, init)
    }

    /**
     *
     * @param path
     * @param body
     * @returns {Observable<R>}
     */
    delete(path: string, options: Object = {}): Observable<any> {
        const httpOptions = Object.assign(options, this.options)
        httpOptions.body = null
        const uri = this.buildUri(path)

        return this.addJwtTokenIfExists()
            .http
            .request('DELETE', uri, httpOptions)
    }

    /**
     *
     * @returns {HttpService}
     */
    addJwtTokenIfExists() {
        const rememberMe = window.localStorage.getItem('rememberMe')

        if (!rememberMe) {
            // look at the typings for the Authorization it allows to prevent following error: error TS2459: Type '{}' has no property 'Authorization' and no string index signature.
            this.options.headers = (({Authorization, ...tails}: {
                Authorization?: string
            }) => (tails))(this.options.headers)

            return this
        }

        const user = JSON.parse(rememberMe)

        /* comment for unit test pass
        if (!user) {
          this.headers.delete('Authorization');
          return this;
        }*/

        if (!Object.keys(this.options.headers).find(prop => prop.toLowerCase() === 'authorization')) {
            this.options.headers['Authorization'] = `Bearer ${user.token}`
        }

        return this
    }

    /**
     * build a clean uri
     * @param path
     * @returns {string}
     */
    buildUri(path: string) {
        let uri = this.baseUrl

        if (path.startsWith('/')) {
            path = path.slice(1)
        }

        if (!uri.endsWith('/')) {
            uri += '/'
        }

        uri += path

        return uri
    }
}
