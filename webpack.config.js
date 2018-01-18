var Encore = require('@symfony/webpack-encore')

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // first, install any presets you want to use (e.g. yarn add babel-preset-es2017)
    // then, modify the default Babel configuration
    // if you prefer using a .babelrc file then this configureBabel will be omit, reactPreset will also need to be loaded with babelrc file
    // or you can use package.json <ith a node babel.presets like this :
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
    .addEntry('js/vuejs', './assets/js/vuejs/app.js')
    .addEntry('js/quasar', './assets/js/quasar/app.js')
    .addEntry('js/login', './assets/js/login/app.js')
    // .addEntry('js/form', './assets/js/form/app.js')
    // .addEntry('js/api-platform-admin-react', './assets/js/api-platform-admin-react/index.js')

    // for specific page css (not managed by vue file per example
    // .addStyleEntry('css/app', './assets/css/app.scss')

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

    // VueJS
    .enableVueLoader()

    // ReactJS
    .enableReactPreset()

    // uncomment for legacy applications that require $/jQuery as a global variable
    // .autoProvidejQuery()

module.exports = Encore.getWebpackConfig()
