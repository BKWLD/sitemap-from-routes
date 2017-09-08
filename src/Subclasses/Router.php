<?php

namespace Bkwld\SitemapFromRoutes\Subclasses;

// Deps
use Illuminate\Routing\Router as LaravelRouter;

/**
 * Subclass Laravel's Router class to create own route instances
 */
class Router extends LaravelRouter {

    /**
     * Create a new Route object.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  mixed  $action
     * @return \Illuminate\Routing\Route
     */
    protected function newRoute($methods, $uri, $action)
    {
        return (new Route($methods, $uri, $action))
                    ->setRouter($this)
                    ->setContainer($this->container);
    }

}
