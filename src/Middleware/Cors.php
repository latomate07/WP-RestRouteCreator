<?php

namespace Latomate07\WpRestRouteCreator\Middleware;

use WP_REST_Request;
use WP_REST_Response;

class Cors
{
    /**
     * Handle the incoming request.
     *
     * @param WP_REST_Request $request The incoming request.
     *
     * @return WP_REST_Request|WP_REST_Response The request object with added CORS headers, or a response object for preflight requests.
     */
    public function handle(WP_REST_Request $request)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($request->get_method() === 'OPTIONS') {
            return new WP_REST_Response('', 204);
        }

        return $request;
    }
}
