export interface UserToken {
  token: string;
  roles: [];
}

export interface TokenServiceI {
  setAccessToken(token: string): TokenServiceI

  getAccessToken(): UserToken

  reset(): TokenServiceI
}
