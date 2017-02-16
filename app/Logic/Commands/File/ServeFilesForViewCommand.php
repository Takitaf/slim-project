<?php

namespace App\Logic\Commands\File;

use App\Logic\Commands\Command;
use Core\Command\ErrorResponse;
use Core\Command\FileResponse;
use \SlimFacades\Container;

class ServeFilesForViewCommand extends Command {

    function validateParameters ($params) {
        return [];
    }

    function makeOperation ($params) {
        $fileUrl = Container::get("request")->getUri()->getPath();
        $url = substr($_SERVER["DOCUMENT_ROOT"], 0, strrpos($_SERVER["DOCUMENT_ROOT"], "/")) . $fileUrl;
        $url = str_replace("/", DIRECTORY_SEPARATOR, $url);
        $file = substr($url, strrpos($url, DIRECTORY_SEPARATOR) + 1);
        if (strpos($file, ".js") == false && strpos($file, ".css") == false) {
            return new ErrorResponse(503);
        }

        if (!file_exists($url)) {
            return new ErrorResponse();
        }
        $contentType = strpos($file, ".js") == true ? "application/javascript" : "text/css";
        return new FileResponse($url, $contentType);
    }

}