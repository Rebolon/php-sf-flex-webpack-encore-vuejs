var Encore = require('@symfony/webpack-encore');

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
    .configureBabel(function(babelConfig) {
        // add additional presets
        babelConfig.presets.push('es2017')

        // no plugins are added by default, but you can add some
        // babelConfig.plugins.push('styled-jsx/babel');
    })

    // uncomment to define the assets of the project
    .addEntry('js/vuejs', './assets/js/vuejs/app.js')
    .addEntry('js/quasar', './assets/js/quasar/app.js')

    // this creates a 'vendor.js' file with common js code
    // these modules will *not* be included in js/vuejs.js or js/quasar.js anymore
    .createSharedEntry('vendor', [
        './assets/js/app.js',

        // you can also extract CSS - this will create a 'vendor.css' file
        // this CSS will *not* be included in vuejs.css anymore
        './assets/css/app.scss'
    ])

    // uncomment if you use Sass/SCSS files
    // parameters are not mandatory, only if webpack build is slow with bootstrap (http://symfony.com/doc/current/frontend/encore/bootstrap.html)
    .enableSassLoader(function(sassOptions) {}, {
        resolve_url_loader: false
    })

    // VueJS
    .enableVueLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    // .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
