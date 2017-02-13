<?php

namespace Core\Router;

use DI\Container;
use FastRoute\RouteParser;

class Router extends \Slim\Router {
    const HTTP_METHODS = ["GET", "POST", "PUT", "DELETE", "OPTIONS", "PATCH", "ANY"];
    private $accessMap = [];

    public function __construct(RouteParser $parser = null) {
        $this->initializeAccessMap();
        parent::__construct($parser);
    }

    private function initializeAccessMap () {
        foreach (self::HTTP_METHODS as $method) {
            $this->accessMap[$method] = [];
        }
    }

    public function route ($methods, $pattern, $command, $auth = ["ANY_USER"]) {
        $callable = $this->prepareRouteAction($command);
        $route = $this->map($methods, $pattern, $callable);
        $this->putRouteIntoAccessMap($methods, $route->getIdentifier(), $auth);
        return $route;
    }

    private function putRouteIntoAccessMap ($methods, $routeIdentifier, $auth) {
        foreach ($methods as $method) {
            $this->accessMap[$method][$routeIdentifier] = $auth;
        }
    }

    public function getAccessRequirements ($request) {
        $method = $request->getMethod();
        $dispatchedRoute = $this->dispatch($request);

        if (empty($dispatchedRoute[1]) || !is_string($dispatchedRoute[1])) {
            return [];
        }

        $routeIdentifier = $dispatchedRoute[1];
        if (!empty($this->accessMap[$method][$routeIdentifier])) {
            return $this->accessMap[$method][$routeIdentifier];
        }

        if (!empty($this->accessMap["ANY"][$routeIdentifier])) {
            return $this->accessMap["ANY"][$routeIdentifier];
        }

        return [];
    }

    private function prepareRouteAction ($command) {
        return function ($request, $response) use ($command) {
            return \SlimFacades\Container::self()->get("\Core\Controllers\Controller")->run($request, $response, $command);
        };
    }
}
