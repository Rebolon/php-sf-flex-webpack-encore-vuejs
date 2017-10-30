# Symfony sample

## requirements

You need PHP (7.x), composer, and npm

## explanation
This application has been realized to get a sample front app with sf3+ & vuejs
Here is how it has been created

* composer create-project symfony/skeleton sf-flex-encore-vuejs
* cd sf-flex-encore-vuejs
* composer require symfony/webpack-encore-pack
* composer require annotations twig api profiler
* yarn add --dev vue vue-loader vue-template-compiler vue-router babel-preset-es2017 sass-loader node-sass bootstrap@4.0.0-beta.2
* yarn install

Then 3 simple php controller has been created on following routes :
 
 * / : DefaultController with route config in routes.yaml
 * /hellow/world : HelloController with route config in annotations and twig template
 * /ghibli : GhibliController with route config in annotations and VueJS app with specific js/css import
 
## components

flex: new symfony system to make web dev life easier ; it works with recipes
vuejs: top js framework to build SPA, or just widget on classic page
encore: symfony solution to wrap webpack config and, once again, make your life simpler
annotations: use annotations everywher in your PHP code
twig: symfony tempalte solution
api: api-platform (instead of fosrestbundle)
profiler: for debugging purpose
babel-preset-es2017: do you really need explanation ?
sass: hey, we are not in nineties, we don't write css now
bootstrap: the beta 4 version of the first class css framework

## run

* install with : composer install & npm install
* Run your application:
  1. Change to the project directory
  2. Execute the `php -S 127.0.0.1:8080 -t public` command;
  3. Browse to the http://localhost:8080/ URL.

    Quit the server with CTRL-C.
    Run composer require symfony/web-server-bundle for a better web server.
    And launch `php bin/console server:start 127.0.0.1:8080`

* Read the documentation at https://symfony.com/doc

## todo

* what is the difference between the dev-server and the build  (package.json ?) coz i don't 
understand how to use them ... the first one seems to watch but doesn't write to disk...
* add quasar (or any other UX library)
* improve this tutorial with an API Route built with Api platform (without DB) and install the vue-generator from api-platform for a crud sample
