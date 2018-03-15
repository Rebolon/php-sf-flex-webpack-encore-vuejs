import { async, TestBed } from '@angular/core/testing'
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing'

import { environment } from '../environments/environment'

import { ApiService } from './api'

describe('Http Service', () => {

    const baseUrl: String = environment.rest.baseUrl
    let httpService: ApiService
    let http: HttpTestingController

    beforeEach(() => TestBed.configureTestingModule({
        imports: [HttpClientTestingModule],
        providers: [ApiService]
    }))

    beforeEach(() => {
        httpService = TestBed.get(ApiService)
        http = TestBed.get(HttpTestingController)
    })

    it('should init the service', () => {
        expect(baseUrl.length).toBeGreaterThan(0, 'Environment api.baseUrl is not defined')
        expect(httpService.baseUrl)
            .toBe(`${baseUrl}`, 'Your service should have a field `baseUrl` correctly initialized')
    })

    it('should do a GET request', async(() => {
        const hardcodedBooks = [{ name: 'Batman vs Superman' }, { name: 'Spirou & Fantasio' }, { name: 'Harry Potter' }]

        httpService.get('/books').subscribe((res) => {
            expect(res).toBe(hardcodedBooks)
        })

        http.expectOne('/books')
            .flush(hardcodedBooks)
    }))

    it('should do a POST request', async(() => {
        const hardcodedBooks = [{ name: 'Batman vs Superman' }, { name: 'Spirou & Fantasio' }, { name: 'Harry Potter' }]

        httpService.post('/books', hardcodedBooks).subscribe((res) => {
            expect(res).toBe(hardcodedBooks)
        })

        http.expectOne('/books')
            .flush(hardcodedBooks)
    }))

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

    it('should do an authenticated GET request', async(() => {
        spyOn(window.localStorage, 'getItem')
            .and.returnValue(JSON.stringify({ token: 'secret' }))

        const hardcodedBooks = [{ name: 'Batman vs Superman' }, { name: 'Spirou & Fantasio' }, { name: 'Harry Potter' }]
        httpService.get('/books').subscribe((res) => {
            expect(res).toBe(hardcodedBooks)
        })
    }))

    it('should do an authenticated DELETE request', async(() => {
        spyOn(window.localStorage, 'getItem')
            .and.returnValue(JSON.stringify({ token: 'secret' }))

        const hardcodedBooks = [{ name: 'Batman vs Superman' }, { name: 'Spirou & Fantasio' }, { name: 'Harry Potter' }]
        httpService.get('/books').subscribe((res) => {
            expect(res).toBe(hardcodedBooks)
        })

        httpService.delete('/books/1').subscribe((res) => {
            expect(res.status).toBe(204, 'The delete method should return the response (and not extract the JSON).')
        })
    }))
})
