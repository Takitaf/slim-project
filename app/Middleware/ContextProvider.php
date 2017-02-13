<?php

namespace Middleware;

use App\Logic\Context\ContextMapper;
use Core\Utilities\AuthenticationHelper;
use Core\Utilities\ContextHelper;
use Dflydev\FigCookies\Cookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Slim\Http\Request;
use Core\Response\Response;

class ContextProvider {
    private $authHelper;
    private $contextHelper;

    public function __construct (AuthenticationHelper $authHelper, ContextMapper $contextMapper, ContextHelper $contextHelper) {
        $this->authHelper = $authHelper;
        $this->contextMapper = $contextMapper;
        $this->contextHelper = $contextHelper;
    }

    public function __invoke (Request $request, Response $response, $next) {
        $response = $next($request, $response);

        if ($request->isXhr() || !$this->authHelper->isLogged()) {
            return $response;
        }

        $userContexts = $this->contextHelper->getUserContexts();
        $currentContext = $this->getCurrentContext($request);
        $userRole = $this->contextHelper->getCurrentContextUserRole();

        $response->addViewParam("userContexts", $userContexts)
                 ->addViewParam("currentContext", $currentContext)
                 ->addViewParam("userRole", $userRole);


        $cookie = SetCookie::create("currentContext", $currentContext)
            ->rememberForever()
            ->withPath("/");
        $response = FigResponseCookies::set($response, $cookie);

        return $response;
    }

    private function getCurrentContext ($request) {
        $currentContext = $this->contextHelper->getCurrentContext();

        if (!is_null($currentContext)) {
            return $currentContext;
        }

        //check request for context
        $cookie = Cookies::fromRequest($request)->get("currentContext");
        if (is_null($cookie)) {
            return null;
        }

        //context found in cookies - set it in session using contextHelper
        $this->contextHelper->setCurrentContext($cookie->getValue());
        return $cookie->getValue();
    }


}