<?php

namespace Bkwld\SitemapFromRoutes;

// Deps
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\URL;
use Roumen\Sitemap\Sitemap as RoumenSitemap;

/**
 * Stores closures for each route added to the sitemap that will later be used
 * to generate sitemap line items.
 */
class Sitemap {

    /**
     * The list of routes that will be sitemapped
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Add a route to the sitemap
     *
     * @param  Illuminate\Routing\Route $route
     * @return void
     */
    public function add(Route $route)
    {
        // Don't act if the sitemap is cached
        if (app('sitemap')->isCached()) return;

        // Open a closure that will capture logic to generate sitemap items from
        $this->routes[] = function(RoumenSitemap $sitemap) use ($route) {
            $this->addRoute($route, $sitemap);
        };
    }

    /**
     * Create sitemap items from a route
     *
     * @param  Illuminate\Routing\Route $route
     * @param  RoumenSitemap $sitemap
     * @return void
     */
    public function addRoute(Route $route, RoumenSitemap $sitemap)
    {
        // See if we can do it simple or not
        $uri = $route->uri();
        if (preg_match('#\{(\w+)\}#', $uri, $matches)) {
            $model = 'App\\'.ucfirst($matches[1]);
            $this->addModels($model, $sitemap);
        } else {
            $sitemap->add(URL::to($uri));
        }
    }

    /**
     * Add public models to the sitemap
     *
     * @param  string $class
     * @param  RoumenSitemap $sitemap
     * @return void
     */
    public function addModels($model, RoumenSitemap $sitemap)
    {
        $model::listing()->get()->each(function($model) use ($sitemap) {
            $sitemap->add($model->uri, $model->updated_at);
        });
    }

    /**
     * Populates the sitemap by looping through all the saved routes
     *
     * @param  RoumenSitemap $sitemap
     * @return void
     */
    public function populate(RoumenSitemap $sitemap)
    {
        foreach($this->routes as $func) {
            $func($sitemap);
        }
    }

}
