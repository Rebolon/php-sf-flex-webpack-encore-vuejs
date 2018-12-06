var Encore = require('@symfony/webpack-encore')
var OfflinePlugin = require('offline-plugin')

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/dist/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/dist')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // first, install any presets you want to use (e.g. yarn add babel-preset-es2017)
    // then, modify the default Babel configuration
    // if you prefer using a .babelrc file then this configureBabel will be omit, reactPreset will also need to be loaded with babelrc file
    // or you can use package.json with a node babel.presets like this :
    /**
     "babel": {
          "presets": [
              [
                  "env",
                  {
                      "targets": {
                          "browsers": [
                            "ie >= 9"
                          ]
                      }
                  }
             ]
          ]
     }
     */
    .configureBabel(function(babelConfig) {
        // add additional presets
        babelConfig.presets.push('es2017')
        babelConfig.presets.push('react')

        // no plugins are added by default, but you can add some
        // babelConfig.plugins.push('styled-jsx/babel');
    })

    // uncomment to define the assets of the project

    // this one was used for this npm package: offline-plugin to manage cache (angular has already its own worker since ng-5.2 & cli-1.6)
    .addEntry('service-worker', './assets/js/lib/service-worker.js')

    .addEntry('js/vuejs', './assets/js/vuejs/app.js')
    .addEntry('js/quasar', './assets/js/quasar/app.js')
    .addEntry('js/login', './assets/js/login/app.js')
    .addEntry('js/form-quasar-vuejs', './assets/js/form-quasar-vuejs/app.js')
    .addEntry('js/form-devxpress-vuejs', './assets/js/form-devxpress-vuejs/app.js')
    .addEntry('js/api-platform-admin-react', './assets/js/api-platform-admin-react/index.js')

    // for specific page css (not managed by vue file per example
    .addStyleEntry('css/quasar-bootstrap', './assets/css/quasar-bootstrap.scss')

    // this creates a 'vendor.js' file with common js code
    // these modules will *not* be included in js/vuejs.js or js/quasar.js anymore
    .createSharedEntry('vendor', [
        './assets/js/app.js',

        // you can also extract CSS - this will create a 'vendor.css' file
        // this CSS will *not* be included in vuejs.css anymore
        './assets/css/app.scss',
    ])

    // uncomment if you use Sass/SCSS files
    // parameters are not mandatory, only if webpack build is slow with bootstrap (http://symfony.com/doc/current/frontend/encore/bootstrap.html)
    .enableSassLoader(function(sassOptions) {}, {
        resolveUrlLoader: false,
    })

    // for quasar styles
    .enableStylusLoader()

    // VueJS
    .enableVueLoader()

    // ReactJS
    .enableReactPreset()

    // uncomment for legacy applications that require $/jQuery as a global variable
    // .autoProvidejQuery()

// customize webpack configuration
let config = Encore.getWebpackConfig();

// https://github.com/NekR/offline-plugin
config.plugins.push(new OfflinePlugin({
    "strategy": "changed",
    "responseStrategy": "cache-first",
    "publicPath": "/dist/",
    "caches": {
        // offline plugin doesn't know about build folder
        // if I added build in it , it will show something like : OfflinePlugin: Cache pattern [build/images/*] did not match any assets
        "main": [
            '*.json',
            '*.css',
            '*.js',
            'img/*'
        ]
    },
    "ServiceWorker": {
        "events": !Encore.isProduction(),
        "entry": "./assets/js/lib/service-worker.js",
        "cacheName": "SymfonyVue",
        "navigateFallbackURL": '/',
        "minify": !Encore.isProduction(),
        "output": "./../service-worker.js",
        "scope": "/"
    },
    "AppCache": null
}));

module.exports = config
