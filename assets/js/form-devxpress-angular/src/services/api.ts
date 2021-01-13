import {Inject, Injectable} from '@angular/core'
import { HttpClient } from '@angular/common/http'
import { Observable } from 'rxjs'
import { environment } from '../environments/environment'
import {apiPlatformPrefix, tokenJwtBearer} from '../../../lib/config'
import { map, tap } from 'rxjs/operators'
import {JwtInterceptorService} from "./jwt-interceptor";
import {TokenService} from "./token.service";
import {UserToken} from "./token.interface";

@Injectable({
  providedIn: 'root'
})
export class ApiService {

    baseUrl = environment.rest.baseUrl

    options = {
        withCredentials: true,
        responseType: 'json',
        body: null,
        headers: {}
    }

    constructor(protected http: HttpClient, protected jwtInterceptorService: JwtInterceptorService, @Inject('TokenServiceJwt') protected tokenStorage: TokenService) {
      this.enableJwtToken();
    }

    get(path: string, options: Object = {}): Observable<any> {
        const init: {} = Object.assign(options, this.options)
        const uri = this.buildUri(path)

        return this.http
            .request('GET', uri, init)
    }

    post(path: string, body: any, options: Object = {}): Observable<any> {
        const init: {} = Object.assign(options, this.options, {body})
        const uri = this.buildUri(path)

        return this.http
            .request('POST', uri, init)
    }

    put(path: string, body: any, options: Object = {}): Observable<any> {
        const init: {} = Object.assign(options, this.options, {body})
        const uri = this.buildUri(path)

        return this.http
            .request('PUT', uri, init)
    }

    delete(path: string, options: Object = {}): Observable<any> {
        const httpOptions: {} = Object.assign(options, this.options, {body: null})
        const uri = this.buildUri(path)

        return this.http
            .request('DELETE', uri, httpOptions)
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

    enableCredentials() {
        this.options.withCredentials = true;
    }

    disableCredentials() {
        this.options.withCredentials = false;
    }

    enableJwtToken() {
        if (this.tokenStorage.getAccessToken()) {
            const user: UserToken = this.tokenStorage.getAccessToken();

            if (user) {
              this.jwtInterceptorService.setJwtToken(user.token)
            }
        }
    }

    disableJwtToken() {
        this.jwtInterceptorService.setJwtToken("")
    }
}
