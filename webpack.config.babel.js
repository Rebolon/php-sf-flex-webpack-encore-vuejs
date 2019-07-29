const Encore = require('@symfony/webpack-encore')
const OfflinePlugin = require('offline-plugin')

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/dist/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/dist')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    /* to copy image and make them available from js code
    .copyFiles({
        from: './assets/images',
         // optional target path, relative to the output dir
         //to: 'images/[path][name].[ext]',

         // if versioning is enabled, add the file hash too
         //to: 'images/[path][name].[hash:8].[ext]',

         // only copy files matching this pattern
         //pattern: /\.(png|jpg|jpeg)$/
     })*/

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

    // enables @babel/preset-env polyfills
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3 // unknown option, but added with latest update of Webpack/Encore
    })

    // uncomment to define the assets of the project

    // this one was used for this npm package: offline-plugin to manage cache (angular has already its own worker since ng-5.2 & cli-1.6)
    .addEntry('service-worker', './assets/js/lib/service-worker.js')

    .addEntry('js/vendor', './assets/js/app.js')
    .addEntry('js/home', './assets/js/home/app.js')
    .addEntry('js/vuejs', './assets/js/vuejs/app.js')
    .addEntry('js/quasar', './assets/js/quasar/app.js')
    .addEntry('js/login', './assets/js/login/app.js')
    .addEntry('js/form-quasar-vuejs', './assets/js/form-quasar-vuejs/app.js')
    .addEntry('js/form-devxpress-vuejs', './assets/js/form-devxpress-vuejs/app.js')
    .addEntry('js/api-platform-admin-react', './assets/js/api-platform-admin-react/App.js')

    // for specific page css (not managed by vue file per example
    .addStyleEntry('css/dx-overload', './assets/css/dx-overload.scss')
    .addStyleEntry('css/quasar-bootstrap', './assets/css/quasar-bootstrap.scss')

    .enableSingleRuntimeChunk()
    .splitEntryChunks()


    // uncomment if you use Sass/SCSS files
    // parameters are not mandatory, only if webpack build is slow with bootstrap (http://symfony.com/doc/current/frontend/encore/bootstrap.html)
    .enableSassLoader(sassOptions => {}, {
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

    .enableTypeScriptLoader()
    // optionally enable forked type script for faster builds
    // https://www.npmjs.com/package/fork-ts-checker-webpack-plugin
    // requires that you have a tsconfig.json file that is setup correctly.
    //.enableForkedTypeScriptTypesChecking()

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
