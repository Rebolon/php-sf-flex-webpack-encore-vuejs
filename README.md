# Symfony sample

## requirements

You need PHP (7.x), composer, and npm

## explanation
This application has been realized to get a sample front app with sf3+ & vuejs
Here is how it has been created

* composer create-project symfony/skeleton sf-flex-encore-vuejs
* cd sf-flex-encore-vuejs
* composer req encore annotations twig api profiler log
* yarn add vue vue-router quasar-framework quasar-extras vuelidate 
* yarn add --dev vue-loader vue-template-compiler vue-router babel-preset-es2017 testcafe sass-loader node-sass bootstrap@4.0.0-beta.2
* yarn install 

Then 3 simple php controller has been created on following routes :
 
 * / : DefaultController with the menu to navigate throught different controllers
 * /demo/simple : SimpleController with route config in routes.yaml and logger injection with autowiring
 * /demo/hello/:name : HelloController with route config in annotations and twig template
 * /demo/vuejs : VuejsController with route config in annotations and VueJS app with specific js/css import
 * /demo/quasar : QuasarController like VuejsController but with the Quasar framework for UX components
 * /login : LoginController managed by Symfony for validation, but managed by the code to render the login form
 * /api : access ApiPlatform api doc
 
## components

flex: new symfony system to make web dev life easier ; it works with recipes
vuejs: top js framework to build SPA, or just widget on classic page
quasar: UX component library based on VueJS
encore: symfony solution to wrap webpack config and, once again, make your life simpler
annotations: use annotations everywhere in your PHP code
twig: symfony template solution, useless if you don't want to render template with symfony, but usefull to be able to use assets twig helper with webpack encore
api: api-platform to build REST api(instead of fosrestbundle)
profiler: for debugging purpose
log: a logger for symfony
babel-preset-es2017: do you really need explanation ?
testcafe: a test framework (might be changed by chimp or anything else)
sass: hey, we are not in nineties, we don't write css now
bootstrap: the beta 4 version of the first class css framework (not used with quasar)

## run

* install the project with `npm run init-project` which will launch :
  1. php dependancies installation: `composer install`
  2. nodejs tooling installation: `npm install`
  3. assets generation: `npm run dev`
  4. db init: `php bin/console doctrine:database:create` & `doctrine:schema:create`
* Run your application:
  1. Change to the project directory
  2. Execute the `npm run dev-server-hot` command to start the asset server that will build your assets and your manifest.json and serve the assets with hot module replacment when you do a modification on a vuejs file 
  2. Execute the `npm run sf-dev` command;
  3. Browse to the http://localhost:80/ URL.

    Quit the server with CTRL-C.
    Run composer require symfony/web-server-bundle for a better web server.
    And launch `php bin/console server:start 127.0.0.1:80`
    
  4. Run frontend tests with `npm run test`

* Read the documentation at https://symfony.com/doc

If you want to change default ports, you can use package.json > config : server_port_web for the web server (php built in server), and server_port_asset for the asset server.
Default ports are 80 and 8080.

## webpack

everything is managed by 'encore' symfony package, so have a look at the webpack.config.js and then read their [docs](http://symfony.com/doc/current/frontend.html)
 * npm run dev : will build your assets (in this project it's /public/build/)
 * npm run watch : does the same thing than npm run dev, but it watches files modifictaion to re-generate the assets
 * npm run dev-server :  build the manifest.json that map your assets qith their url from the asset server and start a web server that will serve those assets
 * npm run dev-server-hot : does the same thing as previously, but with vuejs framework it also does Hot Module Replacement 
 * npm run build : build your assets for production
 
Take care, the asset server listen to port 8080 so don't start your main server on that port, or specify another port for the dev-server using ` --port 9999` for example

Also, if you want to use the asset server finely, you have to add the assets configuration in the config/packages/framework.yaml file :
`json_manifest_path: '%kernel.project_dir%/public/build/manifest.json'`. In fact the npm command will build asset in memory only, and modify the manifest file to map asset to a new url served by the asset server instead of the main web server.

## todo

* improve this tutorial with ~~an API Route built with Api platform (without DB)~~ and install the vue-generator from api-platform for a crud sample
* add db fixtures at init ! almost 40 books and some reviews (at least 3 for 5 1st books)
