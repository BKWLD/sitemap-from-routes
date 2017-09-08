<?php

namespace Bkwld\SitemapFromRoutes\Facades;

use Illuminate\Support\Facades\Facade;
use Bkwld\SitemapFromRoutes\Subclasses\Router;

class Route extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Router::class;
    }

}
