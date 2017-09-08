<?php

namespace Bkwld\SitemapFromRoutes;

// Deps
use App\Http\Controllers\Controller as AppController;
use Illuminate\Http\Request;
use URL;

/**
 * A Laravel controller that creates the sitemap
 */
class Controller extends AppController
{
    /**
     * Generate the sitemap.xml
     *
     * @return Sitemap
     */
    public function index()
    {
        $sitemap = app('sitemap');

        // Check for cached sitemap
        if ($sitemap->isCached()) {
            return $sitemap->render('xml');
        }

        // Add routes to the sitemap object
        app(Sitemap::class)->populate($sitemap);

        // Return the sitemap as XML
        return $sitemap->render('xml');
    }
}
