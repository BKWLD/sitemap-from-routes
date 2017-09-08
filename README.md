# Sitemap from Route

Generate a sitemap directly from your Laravel routes/web.php using [roumen/sitemap](https://github.com/Laravelium/laravel-sitemap).

```php
Route::get('news', 'News@index')->sitemap()
Route::get('news/{article}', 'News@show')->sitemap()
```

This works because, during install, you change the Route facade to point to a class of this package that adds a the fluent `sitemap` to Laravel `Route` instances.  The `sitemap` method looks at the uri of the route and unpacks any model bindings it finds, fetching all public instances of those models and adding them to the sitemap.


## Installation

1. Run `require add bkwld/sitemap-from-routes`
2. Add to `config.app` providers: `Bkwld\SitemapFromRoutes\ServiceProvider::class`
4. Install [roumen/sitemap](https://github.com/Laravelium/laravel-sitemap) assets: `php artisan vendor:publish --provider="Roumen\Sitemap\SitemapServiceProvider"`
5. Add a route for the sitemap to your routes file: `Route::get('sitemap', 'Bkwld\SitemapFromRoutes\Controller@index')`.


## Usage

Call `sitemap()` from any routes you want to add to the sitemap.  For example:

```php
Route::get('news', 'News@index')->sitemap()
Route::get('news/{article}', 'News@show')->name('article')->sitemap()
```

Dynamic route parameters must be named the same as the models they should resolve.  So, in the above example, you must have an `App\Article` model.  

By default, all instances of a model are added to the sitemap by substituting the id of the model into the uri.  Thus, the example route would generate `news/1`, `news/2`, and so on.

### Customize the query

To customize which model instances should be added to the sitemap, specify a `forSitemap` scope on your model.  You would do this to only add public records to the sitemap, for instance.

```
namespace App;
class Article {
    public function scopeForSitemap($query)
    {
        $query->where('public', 1);
    }
}
```

### Customize the URL

To customize the URL that is added to the sitemap for model instances, specify a `sitemapUrl` accessor on your model.  You would do this if you use slugs in your URLs.

```
namespace App;
class Article {
    public function getSitemapUrlAttribute()
    {
        return route('article', $this->slug);
    }
}
```
