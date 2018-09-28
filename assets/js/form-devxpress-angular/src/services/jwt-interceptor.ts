import { Injectable } from '@angular/core';
import { HttpEvent, HttpHandler, HttpInterceptor, HttpRequest } from '@angular/common/http';
import { Observable } from 'rxjs';
import {tokenJwtBearer} from '../../../lib/config'

@Injectable({
  providedIn: 'root'
})
export class JwtInterceptorService implements HttpInterceptor {

  private token: string | null;

  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    if (this.token) {
      const clone = req.clone({ setHeaders: { 'Authorization': `${tokenJwtBearer} ${this.token}` } });
      return next.handle(clone);
    }
    return next.handle(req);
  }

  setJwtToken(token: string) {
    if (token.match(tokenJwtBearer + ' ')) {
      token = token.replace(`${tokenJwtBearer} `, '')
    }

    this.token = token;
  }

  removeJwtToken() {
    this.token = null;
  }
}
