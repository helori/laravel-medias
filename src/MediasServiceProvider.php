<?php

namespace Helori\LaravelMedias;

use Illuminate\Support\ServiceProvider;


class MediasServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/laravel-medias.php', 'laravel-medias'
        );
    }
    
    public function boot()
	{
        $this->loadRoutesFrom(__DIR__.'/routes/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'laravel-medias');
        
        $this->publishes([
            __DIR__.'/config/laravel-medias.php' => config_path('laravel-medias.php'),
        ], 'laravel-medias-config');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/laravel-medias'),
        ], 'laravel-medias-views');

        $this->publishes([
            //__DIR__.'/components' => base_path('resources/assets/js/components/laravel-cms'),
            __DIR__.'/assets/medias.js' => base_path('resources/assets/js/medias.js'),
            __DIR__.'/assets/medias.scss' => base_path('resources/assets/sass/medias.scss'),
        ], 'laravel-medias-assets');
	}
}
