<?php

$app = \SlimFacades\App::self();
$container = \SlimFacades\Container::self();

$app->add($container->get("Middleware\AccessProvider"));
$app->add($container->get("Middleware\LoggedUserProvider"));
$app->add($container->get("Middleware\ContextProvider"));
