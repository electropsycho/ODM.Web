const mix = require('laravel-mix')
const BrowserSyncPlugin = require('browser-sync-webpack-plugin')
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
mix.webpackConfig({
  // plugins: [
  //   new BrowserSyncPlugin({
  //     open: 'external',
  //     host: 'localhost',
  //     port: 8090,
  //     files: ['resources/views/**/*.php', 'app/**/*.php', 'routes/**/*.php']
  //   })
  // ],
  module: {
    rules: [
      {
        test: /\.lang$/,
        loader: 'file-loader'
      }
    ]
  }
})

mix.autoload({
  jquery: ['$', 'window.jQuery', 'jQuery'], // more than one
  moment: 'moment' // only one
})

mix.copyDirectory('resources/images', 'public/images')

mix.js('resources/js/main.js', 'public/js')
  .sass('resources/sass/app.scss', 'public/css')
  .extract(['vue'])
  .sourceMaps()
