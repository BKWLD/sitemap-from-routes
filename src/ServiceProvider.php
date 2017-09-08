<?php

namespace Bkwld\SitemapFromRoutes;

// Deps
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Roumen\Sitemap\SitemapServiceProvider as RoumenProvider;

class ServiceProvider extends BaseServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register Roumen\Sitemap's provider
        $this->app->register(RoumenProvider::class);

        // Bind the sitemap generating class
        $this->app->singleton(Sitemap::class, function($app) {
            return new Sitemap;
        });

        // Replace the router with subclass
        $this->app->singleton('router', function ($app) {
            return new Subclasses\Router($app['events'], $app);
        });
    }

    /**
     * Boot it up
     */
    public function boot()
    {
    }
}
