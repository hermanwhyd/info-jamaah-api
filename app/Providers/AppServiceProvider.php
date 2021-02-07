<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Plank\Mediable\Facades\ImageManipulator;
use Plank\Mediable\ImageManipulation;
use Intervention\Image\Image;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ImageManipulator::defineVariant(
            'thumb',
            ImageManipulation::make(function (Image $image) {
                $image->fit(100, 100);
            })
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\Illuminate\Filesystem\FilesystemManager::class, function ($app) {
            return new \Illuminate\Filesystem\FilesystemManager($app);
        });

        $this->app->singleton('filesystem', function ($app) {
            return $app->loadComponent('filesystems', 'Illuminate\Filesystem\FilesystemServiceProvider', 'filesystem');
        });

        $this->app->bind('Illuminate\Contracts\Filesystem\Factory', function ($app) {
            return new \Illuminate\Filesystem\FilesystemManager($app);
        });
    }
}
