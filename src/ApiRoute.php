<?php

namespace Latomate07\WpRestRouteCreator;

use WP_REST_Request;
use WP_REST_Response;
use Latomate07\WpRestRouteCreator\RouteDefinition;

/**
 * Class for creating and managing custom routes for the WordPress REST API.
 *
 * @package Latomate07\WpRestRouteCreator
 */
class ApiRoute
{
    /**
     * Static array containing global middlewares.
     *
     * @var callable[]
     */
    private static $middlewares = [];

    /**
     * Static array containing middlewares for a group of routes.
     *
     * @var callable[]
     */
    private static $middlewaresGroup = [];

    /**
     * Registers a route with the WordPress REST API.
     *
     * @param string $method The HTTP method for the route (GET, POST, etc.).
     * @param string $endpoint The endpoint URL for the request, with readable parameters.
     * @param callable $callback The callback function to handle the request.
     */
    private static function register($method, $endpoint, $callback)
    {
        add_action('rest_api_init', function () use ($method, $endpoint, $callback) {
            // Convert readable parameters to regex
            $endpointRegex = self::convertReadableParamsToRegex($endpoint);

            register_rest_route('custom/v2', $endpointRegex, [
                'methods' => $method,
                'callback' => function (WP_REST_Request $request) use ($callback) {
                    $request = self::applyMiddleware($request);
    
                    if ($request instanceof WP_REST_Response) {
                        return $request;
                    }
    
                    return $callback($request);
                },
                'permission_callback' => '__return_true',
            ]);
        });
    }

    /**
     * Converts readable parameters in the endpoint URL to WordPress REST API regex.
     *
     * @param string $endpoint The endpoint URL with readable parameters.
     * @return string The endpoint URL with parameters converted to regex.
     */
    private static function convertReadableParamsToRegex($endpoint)
    {
        return preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function ($matches) {
            return '(?P<' . $matches[1] . '>[a-zA-Z0-9-]+)';
        }, $endpoint);
    }

    /**
     * Adds a global middleware to be executed before the route callback.
     *
     * @param callable|object $middleware The middleware function to be added.
     */
    public static function addMiddleware(callable|object $middleware): void
    {
        if (!empty(self::$middlewaresGroup)) {
            if (is_object($middleware) && method_exists($middleware, 'handle')) {
                $middleware = [$middleware, 'handle'];
            }

            self::$middlewaresGroup[] = $middleware;
        } else {
            if (is_object($middleware) && method_exists($middleware, 'handle')) {
                $middleware = [$middleware, 'handle'];
            }

            self::$middlewares[] = $middleware;
        }
    }

    /**
     * Apply middleware to the given request.
     *
     * This method iterates through the registered middleware and applies them to the request object.
     * 
     * @param WP_REST_Request $request The request object to apply middleware to.
     *
     * @return WP_REST_Request|WP_REST_Response The updated request object after applying middleware
     */
    private static function applyMiddleware(WP_REST_Request $request): WP_REST_Request|WP_REST_Response
    {
        foreach (self::$middlewares as $middleware) {
            $result = $middleware($request);
    
            if ($result instanceof WP_REST_Response) {
                return $result;
            }
    
            $request = $result;
        }
    
        return $request;
    }    

    /**
     * Creates a group of routes with specific middlewares.
     *
     * @param callable $callback The function containing the grouped routes.
     */
    public static function group(callable $callback)
    {
        self::$middlewaresGroup = [];

        $callback();

        foreach (self::$middlewaresGroup as $middleware) {
            self::addMiddleware($middleware);
        }

        self::$middlewaresGroup = [];
    }

    /**
     * Registers a GET route with the WordPress REST API.
     *
     * @param string $endpoint The endpoint URL for the request, with readable parameters.
     * @param callable $callback The callback function to handle the request.
     */
    public static function get($endpoint, $callback)
    {
        self::register('GET', $endpoint, $callback);
        if ($callback instanceof Closure) {
            return new RouteDefinition($endpoint, 'GET', $callback);
        }

        return new RouteDefinition($endpoint, 'GET', function (WP_REST_Request $request) use ($callback) {
            return call_user_func($callback, $request);
        });
    }

    /**
     * Registers a POST route with the WordPress REST API.
     *
     * @param string $endpoint The endpoint URL for the request, with readable parameters.
     * @param callable $callback The callback function to handle the request.
     */
    public static function post($endpoint, $callback)
    {
        self::register('POST', $endpoint, $callback);
        if ($callback instanceof Closure) {
            return new RouteDefinition($endpoint, 'POST', $callback);
        }

        return new RouteDefinition($endpoint, 'POST', function (WP_REST_Request $request) use ($callback) {
            return call_user_func($callback, $request);
        });
    }

    /**
     * Registers a PUT route with the WordPress REST API.
     *
     * @param string $endpoint The endpoint URL for the request, with readable parameters.
     * @param callable $callback The callback function to handle the request.
     */
    public static function put($endpoint, $callback)
    {
        self::register('PUT', $endpoint, $callback);
        if ($callback instanceof Closure) {
            return new RouteDefinition($endpoint, 'PUT', $callback);
        }

        return new RouteDefinition($endpoint, 'PUT', function (WP_REST_Request $request) use ($callback) {
            return call_user_func($callback, $request);
        });
    }

    /**
     * Registers a PATCH route with the WordPress REST API.
     *
     * @param string $endpoint The endpoint URL for the request, with readable parameters.
     * @param callable $callback The callback function to handle the request.
     */
    public static function patch($endpoint, $callback)
    {
        self::register('PATCH', $endpoint, $callback);
        if ($callback instanceof Closure) {
            return new RouteDefinition($endpoint, 'PATCH', $callback);
        }

        return new RouteDefinition($endpoint, 'PATCH', function (WP_REST_Request $request) use ($callback) {
            return call_user_func($callback, $request);
        });
    }

    /**
     * Registers a DELETE route with the WordPress REST API.
     *
     * @param string $endpoint The endpoint URL for the request, with readable parameters.
     * @param callable $callback The callback function to handle the request.
     */
    public static function delete($endpoint, $callback)
    {
        self::register('DELETE', $endpoint, $callback);
        if ($callback instanceof Closure) {
            return new RouteDefinition($endpoint, 'DELETE', $callback);
        }

        return new RouteDefinition($endpoint, 'DELETE', function (WP_REST_Request $request) use ($callback) {
            return call_user_func($callback, $request);
        });
    }
}
