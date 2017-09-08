<?php

namespace Bkwld\SitemapFromRoutes;

// Deps
use Illuminate\Database\Eloquent\Model;
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
            $this->addModels($uri, $model, $sitemap);
        } else {
            $sitemap->add(URL::to($uri));
        }
    }

    /**
     * Add public models to the sitemap
     *
     * @param  string $uri
     * @param  string $class
     * @param  RoumenSitemap $sitemap
     * @return void
     */
    public function addModels($uri, $model, RoumenSitemap $sitemap)
    {
        $query = $model::query();
        if (method_exists($model, 'scopeForSitemap')) {
            $query->forSitemap();
        }
        $query->get()->each(function($model) use ($uri, $sitemap) {
            $this->addModel($uri, $model, $sitemap);
        });
    }

    /**
     * Add a single model to the sitemap
     *
     * @param  string $uri
     * @param  Model $model
     * @param  RoumenSitemap $sitemap
     * @return void
     */
    public function addModel($uri, Model $model, RoumenSitemap $sitemap)
    {
        $sitemap->add(
            $this->makeUrl($uri, $model),
            $this->makeDate($model));
    }

    /**
     * Make the URL of the model
     *
     * @param  string $uri
     * @param  Model $model
     * @return string
     */
    public function makeUrl($uri, Model $model)
    {
        if (method_exists($model, 'getSitemapUrlAttribute')) {
            return $model->sitemap_url;
        } else {
            return preg_replace('#\{(\w+)\}#', $model->getKey(), $uri);
        }
    }

    /**
     * Make the updated date
     *
     * @param  Model $model
     * @return Carbon | string
     */
    public function makeDate(Model $model)
    {
        if ($model->usesTimestamps()) {
            $column = $model->getUpdatedAtColumn();
            return $model->$column;
        }
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
