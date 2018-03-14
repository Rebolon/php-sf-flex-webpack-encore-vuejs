import { host } from '../../../lib/config'

export const environment = {
  production: true,
  rest: {
      baseUrl: `http://${host}/api/`
  },
};
