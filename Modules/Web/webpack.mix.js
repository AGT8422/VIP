const {mix} = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public').mergeManifest();

mix.js(__dirname + '/Resources/assets/js/app.js', 'js/web.js')
      .css( __dirname + '/Resources/assets/css/app.css','css/web.css') ;

if (mix.inProduction()) {
    mix.version();
}