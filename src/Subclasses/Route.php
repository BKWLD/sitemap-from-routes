<?php

namespace Bkwld\SitemapFromRoutes\Subclasses;

// Deps
use Bkwld\SitemapFromRoutes\Sitemap;
use Illuminate\Routing\Route as LaravelRoute;

/**
 * Subclass Laravel's Route class to add fluent `sitemap()` method to it
 */
class Route extends LaravelRoute {

    /**
     * Add a route to the sitemap
     *
     * @return $this
     */
    public function sitemap()
    {
        app(Sitemap::class)->add($this);
        return $this;
    }

}