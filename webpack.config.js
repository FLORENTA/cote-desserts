var Encore = require('@symfony/webpack-encore');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const path = require('path');

Encore
    .setOutputPath('web/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(Encore.isProduction())
    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/search_form', './assets/js/js/search_form.js')
    .addEntry('js/article_form', './assets/js/js/article_form.js')
    .addStyleEntry('css/app', './assets/css/app.scss')
    .enableSassLoader()
    .autoProvidejQuery()
    .enableVueLoader()
    .addPlugin(new VueLoaderPlugin())
    .configureBabel(function(babelConfig) {
        // https://github.com/JeffreyWay/laravel-mix/issues/76
        // For ...mapState() use in conjunction with other computed properties
        babelConfig.plugins.push('transform-object-rest-spread');
    });

module.exports = Encore.getWebpackConfig();