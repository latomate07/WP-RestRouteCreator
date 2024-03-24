<?php

use Latomate07\WpRestRouteCreator\ApiRoute;
use Latomate07\WpRestRouteCreator\Middleware\IsUserAuthenticated;
use Latomate07\WpRestRouteCreator\Middleware\RateLimiter;
use Latomate07\WpRestRouteCreator\Middleware\Cors;
use Latomate07\WpRestRouteCreator\Middleware\ApiKeyAuthentication;

uses(TestCase::class);

it('registers a GET route', function () {
    $callback = fn (WP_REST_Request $request) => new WP_REST_Response(['message' => 'Hello, World!'], 200);

    ApiRoute::get('/example', $callback);

    // Perform a request to the registered route and assert the response
    $response = wp_remote_get('http://example.com/wp-json/custom/v2/example');

    expect($response['response']['code'])->toBe(200);
    expect(json_decode($response['body'], true))->toEqual(['message' => 'Hello, World!']);
});

it('applies middleware to a route', function () {
    $callback = fn (WP_REST_Request $request) => new WP_REST_Response(['message' => 'Hello, World!'], 200);

    ApiRoute::get('/example')
        ->middleware([
            new IsUserAuthenticated(),
            new RateLimiter(10),
            new Cors(),
            new ApiKeyAuthentication(['my-api-key']),
        ]);

    $response = wp_remote_get('http://example.com/wp-json/custom/v2/example', [
        'headers' => [
            'X-API-Key' => 'my-api-key',
        ],
    ]);

    expect($response['response']['code'])->toBe(200);
});
