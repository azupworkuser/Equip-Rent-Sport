const mix = require('laravel-mix');
const path = require('path');

require('laravel-mix-tailwind');

mix.babelConfig({
    plugins: ['@babel/plugin-syntax-dynamic-import'],
});

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
// define alias for tenant
mix.webpackConfig({
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js/tenant'),
        }
    }
})

mix
    .js('resources/js/app.js', 'public/js')
    .css('resources/css/app.css', 'public/css')
    .js('resources/js/tenant/app.js', 'public/tenant/js').react()
    .css('resources/css/tenant/app.css', 'public/tenant/css').tailwind()
    .extract(['react', 'react-dom', 'react-router-dom']);

if (mix.inProduction()) {
  mix
   .version();
}
