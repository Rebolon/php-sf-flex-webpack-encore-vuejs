import { Injectable } from '@angular/core'
import { Observable } from 'rxjs/Observable'

import { ApiService } from './api'
import 'rxjs/add/operator/delay'
import 'rxjs/add/operator/do'
import 'rxjs/add/operator/map'
import 'rxjs/add/observable/of'

import { books } from '../mocks/books'
import { ApiMockError } from './api-mock.exception'

@Injectable()
export class ApiServiceMock extends ApiService {
    /**
     *
     * @param path
     * @returns {Observable<any>}
     */
    get(path: string, options: any = {}): Observable<any> {
        const uri = this.buildUri(path)

        if (new URL(uri).pathname.match(/^\/api\/books/)) {
            return Observable.of(books).delay(0)
        }

        throw new ApiMockError(`Unknown route ${uri} from ApiServiceMock::get`)
    }

    /**
     *
     * @param path
     * @param body
     * @returns {Observable<R>}
     */
    post(path: string, body: any, options: Object = {}): Observable<any> {
        const uri = this.buildUri(path)

        if (new URL(uri).pathname.match(/^\/api\/books/)) {
            return Observable.of(books).delay(0)
        }

        throw new ApiMockError(`Unknown route ${uri} from ApiServiceMock::post`)
    }

    /**
     *
     * @param path
     * @param body
     * @returns {Observable<R>}
     */
    delete(path: string, options: Object = {}): Observable<any> {
        const uri = this.buildUri(path)

        throw new ApiMockError(`To be implemented from ApiServiceMock::delete`)
    }
}
