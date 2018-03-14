import { async, inject, TestBed } from '@angular/core/testing'
import { Http, BaseRequestOptions, Response, ResponseOptions, RequestMethod } from '@angular/http'
import { MockBackend } from '@angular/http/testing'

import { environment } from '../environments/environment'

import { ApiService } from './api'

describe('Http Service', () => {

    const baseUrl: String = environment.apollo.baseUrl
    let httpService: ApiService
    let mockBackend: MockBackend

    beforeEach(() => TestBed.configureTestingModule({
        providers: [
            MockBackend,
            BaseRequestOptions,
            {
                provide: Http,
                useFactory: (backend, defaultOptions) => new Http(backend, defaultOptions),
                deps: [MockBackend, BaseRequestOptions]
            },
            ApiService
        ]
    }))

    beforeEach(inject([ApiService, MockBackend], (service: ApiService, backend: MockBackend) => {
        httpService = service
        mockBackend = backend
    }))

    it('should init the service', () => {
        expect(baseUrl.length).toBeGreaterThan(0, 'Environment api.baseUrl is not defined')
        expect(httpService.baseUrl)
            .toBe(`${baseUrl}`, 'Your service should have a field `baseUrl` correctly initialized')
        expect(httpService.options.headers)
            .toBe(httpService.headers, 'Your service should have a field `options` correctly initialized with the headers')
    })

    it('should do a GET request', async(() => {
        const hardcodedStories = [{ name: 'Trump president' }, { name: 'Fear on COP22' }, { name: 'Crash in Vendée globe' }]
        const response = new Response(new ResponseOptions({ body: hardcodedStories }))
        // return the response if we have a connection to the MockBackend
        mockBackend.connections.subscribe(connection => {
            expect(connection.request.url)
                .toBe(`${baseUrl}stories?status=PENDING`, 'The service should build the correct URL for a GET')
            expect(connection.request.method).toBe(RequestMethod.Get)
            expect(connection.request.headers.get('Authorization')).toBeNull()
            connection.mockRespond(response)
        })

        httpService.get('/stories?status=PENDING').subscribe((res) => {
            expect(res).toBe(hardcodedStories)
        })

    }))

    it('should do a POST request', async(() => {
        const hardcodedStories = [{ name: 'Trump president' }, { name: 'Fear on COP22' }, { name: 'Crash in Vendée globe' }]
        const response = new Response(new ResponseOptions({ body: hardcodedStories }))
        // return the response if we have a connection to the MockBackend
        mockBackend.connections.subscribe(connection => {
            expect(connection.request.url)
                .toBe(`${baseUrl}stories`, 'The service should build the correct URL for a POST')
            expect(connection.request.method).toBe(RequestMethod.Post)
            expect(connection.request.headers.get('Authorization')).toBeNull()
            connection.mockRespond(response)
        })

        httpService.post('/stories', hardcodedStories).subscribe((res) => {
            expect(res).toBe(hardcodedStories)
        })

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
        expect(httpService.headers.get('Authorization'))
            .toBe('Bearer secret', 'The `Authorization` header is not correct after adding the JWT token')

        httpService.addJwtTokenIfExists()

        // and no header the second time
        expect(httpService.headers.get('Authorization')).toBeNull('The `Authorization` header should be null after removing the JWT token')
    })

    it('should do an authenticated GET request', async(() => {
        spyOn(window.localStorage, 'getItem')
            .and.returnValue(JSON.stringify({ token: 'secret' }))

        const hardcodedStories = [{ name: 'Trump president' }, { name: 'Fear on COP22' }, { name: 'Crash in Vendée globe' }]
        const response = new Response(new ResponseOptions({ body: hardcodedStories }))
        // return the response if we have a connection to the MockBackend
        mockBackend.connections.subscribe(connection => {
            expect(connection.request.url)
                .toBe(`${baseUrl}stories?status=PENDING`)
            expect(connection.request.method).toBe(RequestMethod.Get)
            expect(connection.request.headers.get('Authorization')).toBe('Bearer secret')
            connection.mockRespond(response)
        })

        httpService.get('/stories?status=PENDING').subscribe((res) => {
            expect(res).toBe(hardcodedStories)
        })

    }))

    it('should do an authenticated DELETE request', async(() => {
        spyOn(window.localStorage, 'getItem')
            .and.returnValue(JSON.stringify({ token: 'secret' }))

        const response = new Response(new ResponseOptions({ status: 204 }))
        // return the response if we have a connection to the MockBackend
        mockBackend.connections.subscribe(connection => {
            expect(connection.request.url)
                .toBe(`${baseUrl}stories/1`)
            expect(connection.request.method).toBe(RequestMethod.Delete)
            expect(connection.request.headers.get('Authorization')).toBe('Bearer secret')
            connection.mockRespond(response)
        })

        httpService.delete('/stories/1').subscribe((res) => {
            expect(res.status).toBe(204, 'The delete method should return the response (and not extract the JSON).')
        })

    }))
})
