<?php

namespace Latomate07\WpRestRouteCreator\Middleware;

use WP_REST_Request;
use WP_REST_Response;

class ApiKeyAuthentication
{
    private $validApiKeys;

    public function __construct(array $validApiKeys)
    {
        $this->validApiKeys = $validApiKeys;
    }

    /**
     * Handle the incoming request.
     *
     * @param WP_REST_Request $request The incoming request.
     *
     * @return WP_REST_Request|WP_REST_Response The request object if the API key is valid, or a response object with an error message.
     */
    public function handle(WP_REST_Request $request)
    {
        $apiKey = $request->get_header('X-API-Key');

        if (!in_array($apiKey, $this->validApiKeys, true)) {
            return new WP_REST_Response(['error' => 'Invalid API key.'], 401);
        }

        return $request;
    }
}
