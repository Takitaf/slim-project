<?php

namespace Middleware;

use Core\Utilities\AuthenticationHelper;
use Dflydev\FigCookies\Cookie;
use Dflydev\FigCookies\Cookies;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\SetCookies;
use Slim\Http\Request;
use Core\Response\Response;

class LoggedUserProvider {
    private $authHelper;

    public function __construct (AuthenticationHelper $authHelper) {
        $this->authHelper = $authHelper;
    }

    public function __invoke (Request $request, Response $response, $next) {
        $response = $next($request, $response);

        if ($request->isXhr()) {
            return $response;
        }

        $response->addViewParam("isLogged", $this->authHelper->isLogged())
                 ->addViewParam("user", $this->authHelper->getLoggedUser());
        return $response;
    }
}