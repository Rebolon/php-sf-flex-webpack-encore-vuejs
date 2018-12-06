import {TestBed, async} from '@angular/core/testing'
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing'
import {HTTP_INTERCEPTORS} from "@angular/common/http";
import {JwtInterceptorService} from "./jwt-interceptor";
import { environment } from '../environments/environment'
import { ApiService } from './api'

describe('Http Service', () => {

    const baseUrl: String = environment.rest.baseUrl
    let httpService: ApiService
    let httpMock: HttpTestingController

    beforeEach(() => {
      TestBed.configureTestingModule({
        imports: [HttpClientTestingModule],
        providers: [
          ApiService,
          {
            provide: HTTP_INTERCEPTORS,
            useClass: JwtInterceptorService,
            multi: true,
          },
        ],
      })

      httpService = TestBed.get(ApiService)
      httpMock = TestBed.get(HttpTestingController)
    })

    afterEach(() => {
      httpMock.verify();
    });

  describe('Test Http Service', () => {
    it('should init the service', () => {
      expect(baseUrl.length).toBeGreaterThan(0, 'Environment api.baseUrl is not defined')
      expect(httpService.baseUrl)
        .toBe(`${baseUrl}`, 'Your service should have a field `baseUrl` correctly initialized')
    })

    it('should do a GET request', () => {
      const hardcodedBooks = [{name: 'Batman vs Superman'}, {name: 'Spirou & Fantasio'}, {name: 'Harry Potter'}]

      httpService.get('/api/books').subscribe((res) => {
        expect(res).toBe(hardcodedBooks)
      })

      httpMock.expectOne(`${baseUrl}books`)
        .flush(hardcodedBooks)

      httpMock.verify();
    })

    it('should do a POST request', () => {
      const hardcodedBooks = [{name: 'Batman vs Superman'}, {name: 'Spirou & Fantasio'}, {name: 'Harry Potter'}]

      httpService.post('/api/books', hardcodedBooks).subscribe((res) => {
        expect(res).toBe(hardcodedBooks)
      })

      httpMock.expectOne(`${baseUrl}books`)
        .flush(hardcodedBooks)

      httpMock.verify();
    })

    it('should do an authenticated GET request', () => {
      // @todo test this when a user service will be created (this is in that service that we have to read/write localStorage
      //spyOn(window.localStorage, 'getItem')
      //  .and.returnValue(JSON.stringify({token: 'secret'}))

      const hardcodedBooks = [{name: 'Batman vs Superman'}, {name: 'Spirou & Fantasio'}, {name: 'Harry Potter'}]
      httpService.get('/api/books').subscribe((res) => {
        expect(res).toBe(hardcodedBooks)
      })

      httpMock
        .expectOne(`${baseUrl}books`)
        .flush(hardcodedBooks)

      // @todo same as above
      // expect(window.localStorage.getItem).toHaveBeenCalled();
    })

    it('should do an authenticated DELETE request', () => {
      // @todo test this when a user service will be created (this is in that service that we have to read/write localStorage
      //spyOn(window.localStorage, 'getItem')
      //  .and.returnValue(JSON.stringify({token: 'secret'}))

      httpService
        .delete('/api/books/1')
        .subscribe()

      httpMock
        .expectOne(`${baseUrl}books/1`)
        .flush({}, {status: 204, statusText: 'OK'})

      // @todo same as above
      // expect(window.localStorage.getItem).toHaveBeenCalled();
    })
  })
})
