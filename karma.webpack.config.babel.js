const Encore = require('@symfony/webpack-encore')

Encore
    .configureRuntimeEnvironment('dev-server')
    // the project directory where compiled assets will be stored
    .setOutputPath('./var/tests/karma/')
    .setPublicPath('/')
    .cleanupOutputBeforeBuild()
    .disableSingleRuntimeChunk()
    .addEntry()

    .enableSassLoader(function(sassOptions) {}, {
        resolveUrlLoader: false,
    })

    .enableVueLoader()

let config = Encore.getWebpackConfig();

module.exports = config
