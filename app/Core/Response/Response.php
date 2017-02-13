<?php
namespace Core\Response;

use SlimFacades\Container;

class Response extends \Slim\Http\Response {
    private $viewArgs;
    private $viewPath;

    public function setView ($path, $args = []) {
        $this->viewPath = $path;
        $this->viewArgs = $args;
        return $this;
    }

    public function hasView () {
        return empty($this->viewPath);
    }

    public function render () {
        if (empty($this->viewPath)) {
            return $this;
        }
        return $this->getTwigView()->render($this, $this->viewPath, $this->viewArgs);
    }

    private function getTwigView () {
        return Container::self()->get("Slim\Views\Twig");
    }

    public function addViewParam ($key, $value) {
        if (empty($this->viewPath)) {
            return $this;
        }

        $this->viewArgs[$key] = $value;
        return $this;
    }

    public function addViewParams (Array $params) {
        foreach ($params as $key => $param) {
            $this->addViewParam($key, $param);
        }
    }
}