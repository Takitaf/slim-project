<?php
namespace Core\Utilities;

use App\Logic\Context\ContextMapper;
use Delight\Cookie\Session;

class ContextHelper {
    private $authHelper;
    private $contextMapper;

    public function __construct (ContextMapper $contextMapper, AuthenticationHelper $authHelper) {
        $this->contextMapper = $contextMapper;
        $this->authHelper = $authHelper;
    }

    public function setCurrentContext ($context) {
        Session::set("currentContext", $context);
    }

    public function getCurrentContext () {
        $currentContext = Session::get("currentContext");

        if (is_null($currentContext)) {
            return null;
        }

        return $currentContext;
    }

    public function getUserContexts () {
        $user = $this->authHelper->getLoggedUser();
        $contexts = Session::get("userContexts");

        if (!is_null($contexts)) {
            return $contexts;
        }

        $contexts = $this->contextMapper->fetchAllContextsByPlayerId($user["userId"]);
        Session::set("userContexts", $contexts);
        return $contexts;
    }

    public function clearUserContexts () {
        Session::delete("userContexts");
    }

    public function getCurrentContextUserRole () {
        $contexts = $this->getUserContexts();
        $context = $this->getCurrentContext();

        $result = array_filter($contexts, function ($element) use ($context) {
            return $element["gameId"] == $context;
        });

        if (empty($result)) {
            return null;
        }
        return array_pop($result)["role"];
    }

    public function isUserAPlayer ($gameId) {
        $contexts = $this->getUserContexts();
        $hasAccess = in_array($gameId, array_column($contexts, "gameId"));
        return $hasAccess;
    }
}