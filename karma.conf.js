const ManifestPlugin = require('webpack-manifest-plugin');
const webpackConfig = require('./karma.webpack.config.babel');
const webpack = require('webpack');

// Configure specific plugin
for (const plugin of webpackConfig.plugins) {
    // Set writeToFileEmit option of the ManifestPlugin to false
    if ((plugin instanceof ManifestPlugin) && plugin.opts) {
        plugin.opts.writeToFileEmit = false
    }
}

// replace mini-css-extract by style-loader ([Encore bug look at here https://github.com/symfony/webpack-encore/issues/256#issuecomment-471233690] until it's fixed in Encore
const styleExtensions = ['/\\.css$/', '/\\.s[ac]ss$/', '/\\.less$/', '/\\.styl$/'];
for (const rule of webpackConfig.module.rules) {
    if (rule.test && rule.oneOf && styleExtensions.includes(rule.test.toString())) {
        rule.oneOf.forEach((oneOf) => {
            oneOf.use[0] = 'style-loader';
        })
    }
}

// Remove entry property (handled by Karma)
delete webpackConfig.entry;

Object.keys(webpackConfig.plugins).forEach((key) => {
    console.log(webpackConfig.plugins[key]);
})

// Karma options
module.exports = function (config) {
    const configuration = {
        browsers: ['Chrome'],

        frameworks: ['jasmine'],

        files: [
            './assets/js/vuejs/tests/index.js'
        ],

        preprocessors: {
            './assets/js/vuejs/tests/index.js': ['webpack']
        },

        webpack: webpackConfig,

        webpackMiddleware: {
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
            outputDir: './var/report', // results will be saved as $outputDir/$browserName.xml
            outputFile: 'karma.xml', // if included, results will be saved as $outputDir/$browserName/$outputFile
            suite: '', // suite will become the package name attribute in xml testsuite element
            useBrowserName: false, // add browser name to report and classes names
            nameFormatter: undefined, // function (browser, result) to customize the name attribute in xml testcase element
            classNameFormatter: undefined, // function (browser, result) to customize the classname attribute in xml testcase element
            properties: {}, // key value pair of properties to add to the <properties> section of the report
            xmlVersion: 1 // use '1' if reporting to be per SonarQube 6.2 XML format
        },

        autoWatch: true,
    };

    if(process.env.TRAVIS){
        configuration.browsers = ['FirefoxHeadless']; // force firefox in Travis coz Chrome is harder to make it works
    }

    config.set(configuration);
}
