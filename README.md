WP Rest Route Creator
=====================

A simple and elegant way to add custom routes to your WordPress REST API for plugin and theme developers.

Features
--------

* Easy-to-use and reusable system for adding custom routes.
* Middleware support for filtering and modifying requests before they reach their destination.
* Group routes with specific middlewares.
* Built-in preset middlewares for common use cases.

Installation
------------

Install the package using Composer:
```javascript
composer require latomate07/wp-rest-route-creator
```
Usage
-----

Here's a basic example of using the `ApiRoute` class to register custom routes:
```php
use Latomate07\WpRestRouteCreator\ApiRoute;

ApiRoute::get('/example', function (WP_REST_Request $request) {
    return new WP_REST_Response(['message' => 'Hello, World!'], 200);
});

ApiRoute::post('/example', function (WP_REST_Request $request) {
    return new WP_REST_Response(['message' => 'Data received: ' . print_r($request->get_params(), true)], 200);
});
```
### Middleware

You can add middleware to your routes for filtering and modifying requests. Here's an example using the built-in `IsUserAuthenticated` middleware:
```php
use Latomate07\WpRestRouteCreator\ApiRoute;
use Latomate07\WpRestRouteCreator\Middleware\IsUserAuthenticated;

ApiRoute::get('/example')
    ->middleware([new IsUserAuthenticated()]);
```
You can chain multiple middleware:
```php
use Latomate07\WpRestRouteCreator\Middleware\RateLimiter;
use Latomate07\WpRestRouteCreator\Middleware\Cors;

ApiRoute::get('/example')
    ->middleware([
        new IsUserAuthenticated(),
        new RateLimiter(10),
        new Cors(),
    ]);
```
#### Built-in Middleware

The package includes several built-in middleware for common use cases:

* `IsUserAuthenticated`: Checks if the user is authenticated.
* `RateLimiter`: Limits the number of requests per minute.
* `Cors`: Adds Cross-Origin Resource Sharing (CORS) headers to the response.
* `ApiKeyAuthentication`: Authenticates requests using an API key.

#### Custom Middleware

You can create your own custom middleware by creating a new class with a `handle` method:
```php
namespace MyPlugin\Middleware;

use WP_REST_Request;
use WP_REST_Response;

class MyCustomMiddleware
{
    public function handle(WP_REST_Request $request)
    {
        // Perform your custom logic here.

        return $request;
    }
}
```
To use your custom middleware, add it to your routes:
```php
use Latomate07\WpRestRouteCreator\ApiRoute;
use MyPlugin\Middleware\MyCustomMiddleware;

ApiRoute::get('/example')
    ->middleware([new MyCustomMiddleware()]);
```
### Route Groups

You can group routes and apply middleware to all routes within the group:
```php
use Latomate07\WpRestRouteCreator\ApiRoute;
use Latomate07\WpRestRouteCreator\Middleware\IsUserAuthenticated;

ApiRoute::group(function () {
    ApiRoute::addMiddleware(new IsUserAuthenticated());

    ApiRoute::get('/forms', [FormController::class, 'index']);
    ApiRoute::post('/forms', [FormController::class, 'store']);
});
```
In this example, the `IsUserAuthenticated` middleware is applied to all routes within the group.

Contributing
------------

Contributions are welcome! Please submit a pull request with your proposed changes.

License
-------

WP Rest Route Creator is released under the MIT License. See the [LICENSE](LICENSE) file for more information.

Support
-------

If you have any questions or need help, please open an issue on GitHub.

---