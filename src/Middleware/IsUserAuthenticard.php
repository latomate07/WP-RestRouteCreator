<?php

namespace Latomate07\WpRestRouteCreator\Middleware;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Middleware to check if the user is authenticated.
 *
 * @package Latomate07\WpRestRouteCreator\Middleware
 */
class IsUserAuthenticated
{
    /**
     * Executes the middleware and checks if the user is authenticated.
     *
     * @param WP_REST_Request $request The request object.
     * @return WP_REST_Request|WP_REST_Response The request object if the user is authenticated, or a response object with an error message.
     */
    public function handle(WP_REST_Request $request)
    {
        if (!is_user_logged_in()) {
            return new WP_REST_Response(['error' => 'User is not authenticated.'], 401);
        }

        return $request;
    }
}
