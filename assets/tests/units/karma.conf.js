const Encore = require('@symfony/webpack-encore')
const ManifestPlugin = require('@symfony/webpack-encore/lib/webpack/webpack-manifest-plugin')
const webpack = require('webpack');

// Initialize Encore before requiring the .config file
Encore.configureRuntimeEnvironment('dev-server')

// Retrieve webpack config
const webpackConfig = require('../../../webpack.config')

// Set writeToFileEmit option of the ManifestPlugin to false
for (const plugin of webpackConfig.plugins) {
    if ((plugin instanceof ManifestPlugin) && plugin.opts) {
        plugin.opts.writeToFileEmit = false
    }
}

// Remove CommonsChunkPlugin: mandatory when we use Encore.createSharedEntry
webpackConfig.plugins = webpackConfig.plugins.filter(plugin => !(plugin instanceof webpack.optimize.CommonsChunkPlugin));

// Remove entry property (handled by Karma)
delete webpackConfig.entry

// Karma options
module.exports = function(config) {
    config.set({
        frameworks: ['jasmine'],

        files: [
            '../../js/vuejs/tests/index.js'
        ],

        preprocessors: {
            '../../js/vuejs/tests/index.js': ['webpack']
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
            require('karma-spec-reporter'),
            require('karma-junit-reporter'),
            require('karma-jasmine-html-reporter'),
        ],

        chromeLauncher: {
            exitOnResourceError: false
        },

        reporters: ['kjhtml', 'spec', 'junit'],

        junitReporter: {
            outputDir: '../../../var/report', // results will be saved as $outputDir/$browserName.xml
            outputFile: 'karma.xml', // if included, results will be saved as $outputDir/$browserName/$outputFile
            suite: '', // suite will become the package name attribute in xml testsuite element
            useBrowserName: false, // add browser name to report and classes names
            nameFormatter: undefined, // function (browser, result) to customize the name attribute in xml testcase element
            classNameFormatter: undefined, // function (browser, result) to customize the classname attribute in xml testcase element
            properties: {}, // key value pair of properties to add to the <properties> section of the report
            xmlVersion: 1 // use '1' if reporting to be per SonarQube 6.2 XML format
        },

        autoWatch: true,
    });
}
