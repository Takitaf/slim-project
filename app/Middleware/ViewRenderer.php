<?php

namespace Middleware;

use Slim\Http\Request;
use Core\Response\Response;

class ViewRenderer {

    public function __invoke (Request $request, Response $response, $next) {
        $response = $next($request, $response);

        if ($response->hasView()) {
            return $response;
        }

        return $response->render()->withHeader('Content-Type', "text/html");
    }
}