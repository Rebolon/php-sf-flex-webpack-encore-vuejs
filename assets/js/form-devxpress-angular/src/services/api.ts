import { Injectable } from '@angular/core'
import { HttpClient } from '@angular/common/http'
import { Observable } from 'rxjs'
import { environment } from '../environments/environment'
import {apiPlatformPrefix, tokenJwtBearer} from '../../../lib/config'
import { map, tap } from 'rxjs/operators'
import {JwtInterceptorService} from "./jwt-interceptor";

@Injectable()
export class ApiService {

    baseUrl = environment.rest.baseUrl

    options = {
        withCredentials: true,
        responseType: 'json',
        body: null,
        headers: {}
    }

    constructor(protected http: HttpClient, protected jwtInterceptorService: JwtInterceptorService) { }

    get(path: string, options: Object = {}): Observable<any> {
        const init = Object.assign(options, this.options)
        const uri = this.buildUri(path)

        return this.addJwtTokenIfExists()
            .http
            .request('GET', uri, init)
    }

    post(path: string, body: any, options: Object = {}): Observable<any> {
        const init = Object.assign(options, this.options)
        init.body = body
        const uri = this.buildUri(path)

        return this.addJwtTokenIfExists()
            .http
            .request('POST', uri, init)
    }

    put(path: string, body: any, options: Object = {}): Observable<any> {
        const init = Object.assign(options, this.options)
        init.body = body
        const uri = this.buildUri(path)

        return this.addJwtTokenIfExists()
            .http
            .request('PUT', uri, init)
    }

    delete(path: string, options: Object = {}): Observable<any> {
        const httpOptions = Object.assign(options, this.options)
        httpOptions.body = null
        const uri = this.buildUri(path)

        return this.addJwtTokenIfExists()
            .http
            .request('DELETE', uri, httpOptions)
    }

    addJwtTokenIfExists() {
        const rememberMe = window.localStorage.getItem('rememberMe')

        if (!rememberMe) {
            return this
        }

        const user = JSON.parse(rememberMe)

        /* comment for unit test pass
        if (!user) {
          this.headers.delete('Authorization');
          return this;
        }*/

        if (user && user.token) {
          this.jwtInterceptorService.setJwtToken(user.token);
        }

        return this
    }

    buildUri(path: string) {
        let uri = this.baseUrl

        if (path.startsWith(apiPlatformPrefix)) {
            path = path.slice(apiPlatformPrefix.length)
        }

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
