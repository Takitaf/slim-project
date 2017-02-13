<?php

$router = \SlimFacades\Container::self()->get("router");

$router->route(["GET"], '/views[/{params:.*}]', App\Logic\Commands\File\ServeFilesForViewCommand::class);

$router->route(["GET"], "/", "public/home/index.twig");
$router->route(["GET"], '/login', "public/login/index.twig", ["GUEST"])->setName("login");
$router->route(["POST"], '/login', App\Logic\Commands\User\LoginCommand::class, ["GUEST"]);
$router->route(["GET"], '/game_list', "public/games/list/index.twig", ["LOGGED_USER"])->setName("public-games");

$router->route(["GET"], '/create_campaign', "public/games/create/index.twig", ["LOGGED_USER"])->setName("create-game");
$router->route(["POST"], '/create_campaign', App\Logic\Commands\Game\CreateGameCommand::class, ["LOGGED_USER"]);
$router->route(["GET"], '/campaign/{id}', "public/games/show/index.twig", ["LOGGED_USER"]);
$router->route(["POST"], '/fetch_campaign', App\Logic\Commands\Game\FetchGameCommand::class, ["LOGGED_USER"]);
$router->route(["POST"], '/switch_context', App\Logic\Commands\User\SetCampaignContextCommand::class, ["LOGGED_USER"]);

$router->route(["GET"], '/map_management/{gameId}', "master/map/index.twig", ["GAME_MASTER"])->setName("modify-map");
$router->route(["POST"], '/master/fetch_map', App\Logic\Commands\Master\FetchMapCommand::class, ["GAME_MASTER"]);
$router->route(["POST"], '/master/save_map', App\Logic\Commands\Master\CreateMapCommand::class, ["GAME_MASTER"]);
$router->route(["POST"], '/master/crop_map', App\Logic\Commands\Master\CropMapCommand::class, ["GAME_MASTER"]);

$router->route(["POST"], '/upload_file', App\Logic\Commands\File\UploadFileCommand::class, ["LOGGED_USER"]);
$router->route(["GET"], '/file/{fileId}/{hash}', App\Logic\Commands\File\GetFileCommand::class, ["LOGGED_USER"]);
