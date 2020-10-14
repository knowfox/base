const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

const knowfox_assets = 'packages/knowfox/knowfox/resources/assets'; 
mix.sourceMaps()
    .js(knowfox_assets + '/js/app.js', 'public/js/knowfox.js')
    .sass(knowfox_assets + '/sass/app.scss', 'public/css/knowfox.css')
    .copyDirectory(knowfox_assets + '/img', 'public/img');
 
mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
    ]);
