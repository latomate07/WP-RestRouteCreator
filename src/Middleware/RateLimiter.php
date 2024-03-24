<?php

namespace Latomate07\WpRestRouteCreator\Middleware;

use WP_REST_Request;
use WP_REST_Response;

class RateLimiter
{
    private $maxRequestsPerMinute;

    public function __construct(int $maxRequestsPerMinute = 60)
    {
        $this->maxRequestsPerMinute = $maxRequestsPerMinute;
    }

    /**
     * Handle the incoming request.
     *
     * @param WP_REST_Request $request The incoming request.
     *
     * @return WP_REST_Request|WP_REST_Response The request object if rate limit is not reached, or a response object with an error message.
     */
    public function handle(WP_REST_Request $request)
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $cacheKey = "rate_limiter:$ipAddress";
        $cachedData = get_transient($cacheKey);

        if ($cachedData === false) {
            set_transient($cacheKey, ['count' => 1, 'timestamp' => time()], 60);
            return $request;
        }

        if ($cachedData['timestamp'] + 60 <= time()) {
            set_transient($cacheKey, ['count' => 1, 'timestamp' => time()], 60);
            return $request;
        }

        if ($cachedData['count'] >= $this->maxRequestsPerMinute) {
            return new WP_REST_Response(['error' => 'Too many requests.'], 429);
        }

        $cachedData['count']++;
        set_transient($cacheKey, $cachedData, 60);

        return $request;
    }
}
