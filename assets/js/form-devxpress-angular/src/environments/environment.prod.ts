// @ts-ignore
import { host, apiPlatformPrefix } from '../../../lib/config'

export const environment = {
  production: true,
  rest: {
    baseUrl: `//${host}${apiPlatformPrefix}/`
  },
};
