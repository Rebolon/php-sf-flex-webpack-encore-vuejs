import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable } from 'rxjs';
import {catchError, tap} from 'rxjs/operators';

import { environment } from '../../../environments/environment';
import { UserModel } from '../../../models/user.model';
import { JwtInterceptorService } from '../../../services/jwt-interceptor';
import {loginInfos, host} from "../../../../../lib/config";

@Injectable({
  providedIn: 'root'
})
export class User {

  private userEvents = new BehaviorSubject<UserModel>(undefined);
  public  user = this.userEvents.asObservable();

  constructor(private http: HttpClient, private jwtInterceptorService: JwtInterceptorService) {
    this.retrieveUser();
  }

  register(login: string, password: string, birthYear: number): Observable<UserModel> {
    const body = { login, password, birthYear };
    return this.http.post<UserModel>(`${environment.rest.baseUrl}/api/users`, body);
  }

  authenticate(credentials: { login: string; password: string }): Observable<UserModel> {
    const jwtLoginUri = `//${host}${loginInfos.uriLogin.jwt}`

    // it seems that if you don't type the body, then it won't be sent over POST HTTP
    const formatedCredentials: {} = {}
    formatedCredentials[loginInfos.loginUsernamePath] = credentials.login
    formatedCredentials[loginInfos.loginPasswordPath] = credentials.password

    return this.http.post<UserModel>(jwtLoginUri, formatedCredentials).pipe(
      tap(user => this.storeLoggedInUser(user))
    );
  }

  storeLoggedInUser(user: UserModel) {
    window.localStorage.setItem('rememberMe', JSON.stringify(user));
    this.jwtInterceptorService.setJwtToken(user.token);
    this.userEvents.next(user);
  }

  retrieveUser() {
    const value = window.localStorage.getItem('rememberMe');
    if (value) {
      const user = JSON.parse(value);
      this.jwtInterceptorService.setJwtToken(user.token);
      this.userEvents.next(user);
    }
  }

  logout() {
    this.userEvents.next(null);
    window.localStorage.removeItem('rememberMe');
    this.jwtInterceptorService.removeJwtToken();
  }

}
