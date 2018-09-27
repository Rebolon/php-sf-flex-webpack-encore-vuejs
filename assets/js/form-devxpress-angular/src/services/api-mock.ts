import { Injectable } from '@angular/core'
import { Observable, of } from 'rxjs'

import { ApiService } from './api'
import { tap, map, delay } from 'rxjs/operators'

import { books } from '../mocks/books'
import { jobs } from '../mocks/jobs'
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
            return of(books).pipe(delay(0))
        }

        if (new URL(uri).pathname.match(/^\/api\/jobs/)) {
            return of(jobs).pipe(delay(0))
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
            return of(books).pipe(delay(0))
        }

        if (new URL(uri).pathname.match(/^\/api\/jobs/)) {
            return of(jobs).pipe(delay(0))
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
