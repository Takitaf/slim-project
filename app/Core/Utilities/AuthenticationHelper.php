<?php
namespace Core\Utilities;

use App\Logic\Context\ContextMapper;
use Delight\Auth\Auth;
use Delight\Cookie\Session;
use SlimFacades\Container;

class AuthenticationHelper {
    const INVALID_EMAIL     = "invalid_email";
    const INVALID_PASSWORD  = "invalid_password";
    const INACTIVE_EMAIL    = "inactive_email";
    const TOO_MANY_REQUESTS = "too_many_requests";

    private $db;
    private $authLib;
    private $contextHelper;

    static private $instance;

    private function __construct () {
        $this->db = Container::self()->get("Core\Database\DatabaseConnector");
        $this->authLib = new Auth($this->db->getConnection());
        $this->contextHelper = new ContextHelper(new ContextMapper($this->db), $this);
    }

    static public function getInstance () {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function register ($email, $password, $username = null) {
        $result = null;
        try {
            $result = $this->authLib->register($email, $password, $username);
        } catch (\Delight\Auth\InvalidEmailException $e) {
            // invalid email address
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            // invalid password
        } catch (\Delight\Auth\UserAlreadyExistsException $e) {
            // user already exists
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            // too many requests
        }
        return $result;
    }

    public function login ($email, $password) {
        try {
            $this->authLib->login($email, $password, (int) (60 * 60 * 24 * 365.25));
            $this->rememberUserRoles();
        } catch (\Delight\Auth\InvalidEmailException $e) {
            return self::INVALID_EMAIL;
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            return self::INVALID_PASSWORD;
        } catch (\Delight\Auth\EmailNotVerifiedException $e) {
            return self::INACTIVE_EMAIL;
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            return self::TOO_MANY_REQUESTS;
        }

        return true;
    }

    private function rememberUserRoles () {
        $userId = $this->authLib->getUserId();
        $result = $this->db->query("SELECT r.name FROM users_roles ur
                                    JOIN roles r ON ur.role_id = r.role_id
                                    WHERE ur.user_id = ?", array($userId));
        Session::set("roles", array_column($result, "name"));
    }

    public function getLoggedUser () {
        if ($this->isLogged()) {
            return array(
                "userId"   => $this->authLib->getUserId(),
                "email"    => $this->authLib->getEmail(),
                "username" => $this->authLib->getUsername()
            );
        }
        return null;
    }

    public function getUserRoles () {
        if ($this->isLogged()) {
            $roles = Session::get("roles");
            $roles[] = "LOGGED_USER";
            $contextRole = $this->contextHelper->getCurrentContextUserRole();
            if (!empty($contextRole)) {
                $roles[] = $contextRole;
            }
        } else {
            $roles = ["GUEST"];
        }
        return $roles;
    }

    public function isLogged () {
        return $this->authLib->check();
    }

    public function logout () {
        $this->authLib->logout();
    }
}