import { ApolloClient } from 'apollo-client'
import { HttpLink } from 'apollo-link-http'
import { InMemoryCache } from 'apollo-cache-inmemory'
import VueApollo from 'vue-apollo'
import { host } from './config'

const httpLink = new HttpLink({
  // Take care : if you change the api endpoint in config/packages/api_platform.yaml you need to change it here
  // You should use an absolute URL here
  uri: `http://${host}/api/graphql`,
})

// Create the apollo client
export const apolloClient = new ApolloClient({
  link: httpLink,
  cache: new InMemoryCache(),
  connectToDevTools: true,
})

export const apolloProvider = new VueApollo({
  defaultClient: apolloClient,
})
