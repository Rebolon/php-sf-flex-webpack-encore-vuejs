import {async, fakeAsync, TestBed, tick} from '@angular/core/testing'
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing'

import { environment } from '../environments/environment'

import { ApiService } from './api'

describe('Http Service', () => {
  const baseUrl: String = environment.rest.baseUrl
  let httpService: ApiService
  let httpMock: HttpTestingController

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [ApiService]
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
      const hardcodedBooks = [{ name: 'Batman vs Superman' }, { name: 'Spirou & Fantasio' }, { name: 'Harry Potter' }]

      httpService
        .get('/api/books')
        .subscribe((res) => {
          expect(res).toBe(hardcodedBooks)
        })

      httpMock
        .expectOne(`${baseUrl}books`)
        .flush(hardcodedBooks)
    })

    it('should do a POST request', () => {
      const hardcodedBooks = [{ name: 'Batman vs Superman' }, { name: 'Spirou & Fantasio' }, { name: 'Harry Potter' }]

      httpService
        .post('/api/books', hardcodedBooks)
        .subscribe((res) => {
          expect(res).toBe(hardcodedBooks)
        })

      httpMock
        .expectOne(`${baseUrl}books`)
        .flush(hardcodedBooks)
    })

    it('should add/remove the JWT token to the headers', () => {
      // will first return a 'secret' token, then nothing on second call
      let firstCall = true
      spyOn(window.localStorage, 'getItem').and.callFake(() => {
        if (firstCall) {
          firstCall = false

          return JSON.stringify({ token: 'secret' })
        }

        return null
      })

      httpService.addJwtTokenIfExists()

      // so we should have a header the first time
      expect(httpService.options.headers['Authorization'])
        .toBe('Bearer secret', 'The `Authorization` header is not correct after adding the JWT token')

      httpService.addJwtTokenIfExists()

      // and no header the second time
      expect(httpService.options.headers['Authorization']).toBeNull('The `Authorization` header should be null after removing the JWT token')
    })

    it('should do an authenticated GET request', () => {
      spyOn(window.localStorage, 'getItem')
        .and.returnValue(JSON.stringify({ token: 'secret' }))

      const hardcodedBooks = [{ name: 'Batman vs Superman' }, { name: 'Spirou & Fantasio' }, { name: 'Harry Potter' }]
      httpService
        .get('/api/books')
        .subscribe((res) => {
          expect(res).toBe(hardcodedBooks)
        })

      httpMock
        .expectOne(`${baseUrl}books`)
        .flush(hardcodedBooks)

      expect(window.localStorage.getItem).toHaveBeenCalled();
      // @todo: how to check localStorage
    })

    it('should do an authenticated DELETE request', () => {
      spyOn(window.localStorage, 'getItem')
        .and.returnValue(JSON.stringify({ token: 'secret' }))

      httpService
        .delete('/api/books/1')
        .subscribe()

      httpMock
        .expectOne(`${baseUrl}books/1`)
        .flush({}, {status: 204, statusText: 'OK'})

      expect(window.localStorage.getItem).toHaveBeenCalled();
    })
  })
})
