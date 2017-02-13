<?php

namespace Middleware;

use Core\Router\Router;
use Core\Utilities\AuthenticationHelper;
use Monolog\Logger;
use Slim\Http\Request;
use Core\Response\Response;

class AccessProvider {
    private $router;
    private $authHelper;

    public function __construct (AuthenticationHelper $authHelper, Router $router) {
        $this->authHelper = $authHelper;
        $this->router = $router;
    }

    public function __invoke (Request $request, Response $response, $next) {
        $userRoles = $this->authHelper->getUserRoles();
        $result = $this->router->getAccessRequirements($request);
        if (empty($result)) {
            return $response->withStatus(404);
        }

        if (!in_array("ANY_USER", $result) && count(array_intersect($result, $userRoles)) == 0) {
            return $response->withRedirect("/");
        }

        $response = $next($request, $response);
        return $response;
    }
}