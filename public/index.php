<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

use SlimFacades\Facade;
use SlimFacades\Route;
use SlimFacades\App;
$app = new Core\App();
Facade::setFacadeApplication($app);

require __DIR__ . '/../bootstrap/middleware.php';
require __DIR__ . '/../bootstrap/routes.php';


/*
    This is the "unique magic" of our Slim 3 Skeleton. We use our very own Response class (Core\Response\Response) which
    allows us to render view AFTER all Middlewares. In fact, it happens in the last Middleware (under this comment).

    WARNING! IT MUST BE THE LAST LINE BEFORE APPLICATION RUN.

    It ensures us that all other middlewares happen before that particular one, which render Twig template. It makes it
    a lot easier to extend our template scope in Middlewares (we can e.g. add logged user data to every template in a
    new LoggedProvider)
*/
\SlimFacades\App::self()->add(\SlimFacades\Container::self()->get("\Middleware\ViewRenderer"));

App::run();
