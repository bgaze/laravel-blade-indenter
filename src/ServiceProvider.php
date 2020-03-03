<?php

namespace Bgaze\BladeIndenter;

use Exception;
use Illuminate\Support\ServiceProvider as Base;

/**
 * The package service provider
 *
 * @author bgaze <benjamin@bgaze.fr>
 */
class ServiceProvider extends Base
{

    /**
     * Bootstrap the package services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish configuration.
        $this->publishes([__DIR__ . '/config/blade-indenter.php' => config_path('blade-indenter.php')], 'blade-indenter-config');

        // Configure indenter service.
        resolve(BladeIndenter::class)
            ->setSelfClosingTags(config('blade-indenter.self_closing_tags'))
            ->setSelfClosingDirectives(config('blade-indenter.self_closing_directives'))
            ->setClosingDirectives(config('blade-indenter.closing_directives'))
            ->setElseDirectives(config('blade-indenter.else_directives'));
    }


    /**
     * Register the package services.
     *
     * @return void
     * @throws Exception
     */
    public function register()
    {
        // Merge package configuration.
        $this->mergeConfigFrom(__DIR__ . '/config/blade-indenter.php', 'blade-indenter');

        // Register indenter service.
        $this->app->singleton(BladeIndenter::class, function ($app) {
            return new BladeIndenter();
        });
    }
}
