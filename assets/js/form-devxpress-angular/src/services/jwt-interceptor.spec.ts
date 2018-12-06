import { TestBed } from '@angular/core/testing';
import {
  HttpClientTestingModule,
  HttpTestingController,
} from '@angular/common/http/testing';
import { ApiService } from './api';
import { JwtInterceptorService } from './jwt-interceptor';
import { HTTP_INTERCEPTORS } from '@angular/common/http';
import {environment} from "../environments/environment";

describe(`JwtInterceptorService`, () => {
  const baseUrl: String = environment.rest.baseUrl
  let service: ApiService;
  let httpMock: HttpTestingController;

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
    });

    service = TestBed.get(ApiService);
    httpMock = TestBed.get(HttpTestingController);
  })

  afterEach(() => {
    httpMock.verify();
  });

  describe('Testing JwtInterceptorsService', () => {

    it('should add/remove the JWT token to the headers', () => {
      const hardcodedBooks = [{name: 'Batman vs Superman'}, {name: 'Spirou & Fantasio'}, {name: 'Harry Potter'}]

      service
        .get('/api/books')
        .subscribe(response => {
          expect(response).toBeTruthy();
        });

      const httpRequest = httpMock
        .expectOne(`${baseUrl}books`);

      httpRequest
        .flush({}, {status: 204, statusText: 'OK'});

      expect(httpRequest.request.headers.has('Authorization')).toEqual(true);
    });
  })
})
