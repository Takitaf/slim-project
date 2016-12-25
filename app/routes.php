<?php
// Routes
$app->get('/views[/{params:.*}]', function ($request, $response, $args) {
    $url = "../views/" . $args["params"];
    $url = str_replace("/", DIRECTORY_SEPARATOR, $url);
    $file = substr($url, strrpos($url, DIRECTORY_SEPARATOR) + 1);
    if (strpos($file, ".js") == false && strpos($file, ".css") == false && strpos($file, ".jst") == false) {
        return $response->withStatus(503);
    }

    if (!file_exists($url)) {
        return $response->withStatus(404);
    }

    $response = $response->withHeader('Content-Description', 'File Transfer')
        ->withHeader('Content-Type', 'application/octet-stream')
        ->withHeader('Content-Disposition', 'attachment;filename="'.basename($url).'"')
        ->withHeader('Expires', '0')
        ->withHeader('Cache-Control', 'must-revalidate')
        ->withHeader('Pragma', 'public')
        ->withHeader('Content-Length', filesize($url));

    readfile($url);
    return $response;
});

/*$app->get('/admin/list', function ($request, $response, $args) {
    return $this->renderer->render($response, 'admin/game/list/index.twig', $args);
});*/

$app->get('/', function ($request, $response, $args) {
    return $this->renderer->render($response, 'admin/game/list/index.twig', $args);
});
