import { Injectable } from '@angular/core';
import {
  HttpErrorResponse,
  HttpEvent,
  HttpHandler,
  HttpInterceptor,
  HttpRequest,
  HttpResponse
} from '@angular/common/http';
import {Observable, throwError} from 'rxjs';
import {map, catchError} from "rxjs/operators";
import {tokenJwtBearer} from '../../../lib/config'

@Injectable({
  providedIn: 'root'
})
export class JwtInterceptorService implements HttpInterceptor {

  private token: string | null;

  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    let clone = req;
    let handle;

    if (this.token) {
      clone = req.clone({ setHeaders: { 'Authorization': `${tokenJwtBearer} ${this.token}` } });
    }

    handle = next.handle(clone).pipe(
      map((event: HttpEvent<any>) => {
        if (event instanceof HttpResponse) {
          // console.log('interceptor : event--->>>', event);
          // this.errorDialogService.openDialog(event);
        }
        return event;
      }),
      catchError((error: HttpErrorResponse) => {
        if (error.status === 403) {
          // @todo ask for login modal maybe using an Observable, but is it this class responsability ? not sure
        }

        return throwError(error);
      }));

    return handle;
  }

  setJwtToken(token: string) {
    if (token.startsWith(tokenJwtBearer)) {
      token = token.replace(`${tokenJwtBearer} `, '');
    }

    this.token = token;
  }

  getJwtToken() {
    return this.token
  }

  removeJwtToken() {
    this.token = null;
  }

  readJwt(token) {
    try {
      return JSON.parse(atob(token.split('.')[1]));
    } catch (e) {
      return null;
    }
  }
}
