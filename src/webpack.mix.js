const mix = require('laravel-mix');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix .js('resources/vendor/gnu/js/gnu.js', 'public/js')
    .webpackConfig({
        output: {
            //publicPath: 'public/',
            chunkFilename: 'js/lazy/[name].js',
            //chunkFilename: 'js/lazy/[name].[chunkhash].js',
        }
    });
