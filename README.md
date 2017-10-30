# Symfony sample

## explanation
This application has been realized to get a sample front app with sf3+ & vuejs
Here is how it has been created

* composer create-project symfony/skeleton sf-flex-encore-vuejs
* cd sf-flex-encore-vuejs
* composer require symfony/webpack-encore-pack
* composer require annotations twig api profiler
* yarn add --dev vue vue-loader vue-template-compiler vue-router babel-preset-es2017 sass-loader node-sass bootstrap@4.0.0-beta.2
* yarn install

## components

flex:
vuejs:
encore:
annotations:
twig:
api:
profiler:
babel-preset-es2017:
sass:
bootstrap:

## run

* Run your application:
  1. Change to the project directory
  2. Execute the `php -S 127.0.0.1:8080 -t public` command;
  3. Browse to the http://localhost:8080/ URL.

    Quit the server with CTRL-C.
    Run composer require symfony/web-server-bundle for a better web server.
    And launch `php bin/console server:start 127.0.0.1:8080`

* Read the documentation at https://symfony.com/doc

## todo

* how to init the vue components ?
* do we build a perPage.js file to import by PHP controller ? same for CSS ?
* what is the difference between the dev-server and the build  (package.json ?) coz i don't 
understand how to use them ... the first one seems to watch but doesn't write to disk...
* improve this tutorial with an API Route built with Api platform (without DB)  