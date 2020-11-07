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

/* mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css'); */

mix.styles([
        'resources/vendor/fontawesome/css/all.min.css',
        'resources/vendor/css/adminlte.min.css',
        'resources/vendor/css/myselldepot.css',
    ], 'public/css/template.css')
    .js('resources/js/app.js', 'public/js') //JQuery, Bootstrap, VueJS
    .scripts([
        'resources/vendor/js/adminlte.min.js',
        'resources/vendor/js/demo.js',
    ], 'public/js/template.js')
    .copy('resources/vendor/fontawesome/webfonts', 'public/webfonts');
