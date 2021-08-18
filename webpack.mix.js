const mix = require('laravel-mix');

mix.setPublicPath('./public');

mix.js('resources/js/auth.js', 'public/modules/portal-auth/js');

if(!mix.inProduction()) {
    mix.sourceMaps();
}

mix.webpackConfig({
    externals: {
        '@bristol-su/frontend-toolkit': 'Toolkit',
    }
});
