import { Injectable } from '@angular/core'
import {TokenServiceI, UserToken} from "./token.interface";

@Injectable()
export class TokenService implements TokenServiceI {
  constructor(private TOKEN_KEY: string = 'tokenKey') { }

  setAccessToken(token: string): TokenServiceI {
    localStorage.setItem(this.TOKEN_KEY, token)

    return this
  }

  getAccessToken(): UserToken|null {
    if (!localStorage) {
      throw new Error('LocalStorage is not available in the current browser')
    }

    const userToken = localStorage.getItem(this.TOKEN_KEY)

    return userToken ? JSON.parse(userToken) : null
  }

  reset(): TokenServiceI {
    localStorage.removeItem(this.TOKEN_KEY)

    return this
  }
}
