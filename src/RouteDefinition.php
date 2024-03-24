<?php 

namespace Latomate07\WpRestRouteCreator;

use Closure;

class RouteDefinition
{
    protected $endpoint;
    protected $method;
    protected $callback;
    protected $middlewares = [];

    public function __construct($endpoint, $method, Closure $callback)
    {
        $this->endpoint = $endpoint;
        $this->method = $method;
        $this->callback = $callback;
    }

    public function middleware(array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            ApiRoute::addMiddleware($middleware);
        }
        return $this;
    }
}