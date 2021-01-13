import { Injectable } from '@angular/core'
import {TokenServiceI} from "./token.interface";

@Injectable()
export class TokenMockService implements TokenServiceI {
  protected token = null

  constructor(private TOKEN_KEY: string = 'tokenKey') { }

  setAccessToken(token: string): TokenServiceI {
    this.token = token

    return this
  }

  getAccessToken(): string {
    return this.token
  }

  reset(): TokenServiceI {
    this.token = null

    return this
  }
}
