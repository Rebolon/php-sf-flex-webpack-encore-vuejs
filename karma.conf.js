const Encore = require('@symfony/webpack-encore')
const ManifestPlugin = require('@symfony/webpack-encore/lib/webpack/webpack-manifest-plugin')

// Initialize Encore before requiring the .config file
Encore.configureRuntimeEnvironment('dev-server')

// Retrieve webpack config
const webpackConfig = require('./webpack.config')

// Set writeToFileEmit option of the ManifestPlugin to false
for (const plugin of webpackConfig.plugins) {
    if ((plugin instanceof ManifestPlugin) && plugin.opts) {
        plugin.opts.writeToFileEmit = false
    }
}

// Remove entry property (handled by Karma)
delete webpackConfig.entry

// Karma options
module.exports = function(config) {
    config.set({
        frameworks: ['jasmine'],

        files: [
            'assets/js/vuejs/tests/index.js'
        ],

        preprocessors: {
            'assets/js/vuejs/tests/index.js': ['webpack']
        },

        browsers: ['Chrome'],

        webpack: webpackConfig,

        webpackMiddleware: {
            stats: 'errors-only',
            noInfo: true,
        },

        plugins: [
            require('karma-webpack'),
            require('karma-jasmine'),
            require('karma-chrome-launcher'),
            require('karma-spec-reporter')
        ],

        phantomjsLauncher: {
            exitOnResourceError: true
        },

        reporters: ['spec']
    });
}
