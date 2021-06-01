let mix = require('laravel-mix');

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

mix.js('resources/assets/js/home.js', 'public/js/build').vue({ version: 2 })
mix.js('resources/assets/js/profile.js', 'public/js/build').vue({ version: 2 })
mix.sass('resources/assets/sass/app.scss', 'public/css/build');

if (mix.inProduction()) {
    mix.version().disableNotifications();
} else {
    // mix.browserSync({
    //     proxy: process.env.APP_URL,
    //     files: [
    //         'app/**/*.php',
    //         'resources/views/**/*.php',
    //         'public/js/build/*.js',
    //         'public/css/**/*.css'
    //     ]
    // })
}



