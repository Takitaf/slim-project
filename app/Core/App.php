<?php
namespace Core;

use Core\Database\DatabaseConnector;
use Core\Response\Response;
use Core\Router\Router;
use Core\Utilities\AuthenticationHelper;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class App extends \DI\Bridge\Slim\App {

    protected function configureContainer (ContainerBuilder $builder) {
        $builder->addDefinitions(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "bootstrap" . DIRECTORY_SEPARATOR . "settings.php");
        $builder->addDefinitions([
            \Slim\Views\Twig::class => function (\Interop\Container\ContainerInterface $c) {
                $settings = $c->get("config")["renderer"];
                $view = new \Slim\Views\Twig($settings["template_path"]);

                $basePath = rtrim(str_ireplace('index.php', '', $c->get('request')->getUri()->getBasePath()), '/');
                $view->addExtension(new \Slim\Views\TwigExtension($c->get('router'), $basePath));
                return $view;
            },
            \Core\Utilities\Registry::class => function () {
                return Utilities\Registry::getInstance();
            },
            Utilities\AuthenticationHelper::class => function () {
                return AuthenticationHelper::getInstance();
            },
            DatabaseConnector::class => function () {
                return DatabaseConnector::getInstance();
            },
            "response" => function () {
                return new Response();
            },
            'router' => \DI\object(Router::class)
                ->method('setCacheFile', \DI\get('settings.routerCacheFile')),
            \Core\Router\Router::class => \DI\get('router'),
            \Monolog\Logger::class => function (\Interop\Container\ContainerInterface $c) {
                $log = new Logger("DebugLog");
                $log->pushHandler(new StreamHandler($c->get("Core\Utilities\Registry")->getProjectRootDirectory() . '/logs/my-log.log', Logger::WARNING));
                return $log;
            }
        ]);
    }
}