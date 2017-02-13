<?php
namespace Core\Controllers;

use Core\Command\FileResponse;
use Core\Response\Response;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;

class Controller {
    const VIEW_COMMAND = "view";
    const STANDARD_COMMAND = "command";

    private $container;

    public function __construct (ContainerInterface $container) {
        $this->container = $container;
    }

    public function run (Request $request, Response $response, $command) {
        if ($this->recognizeCommand($command) == self::VIEW_COMMAND) {
            $response->setView($command);
            $response->addViewParams($this->prepareDataBasedOnRequest($request));
            return $response;
        }

        $commandResponse = $this->container->get($command)->execute($this->prepareDataBasedOnRequest($request));
        if ($commandResponse->hasErrors()) {
            return $response->withJson($commandResponse->getResult(), $commandResponse->getErrorCode());
        };

        if ($commandResponse->hasFile()) {
            return $this->prepareFileResponse($response, $commandResponse);
        }

        return $response->withJson($commandResponse->getResult(), 200);
    }

    private function recognizeCommand ($command) {
        if (class_exists($command)) {
            return self::STANDARD_COMMAND;
        }

        if (is_string($command) && strpos($command, ".twig") > -1) {
            return self::VIEW_COMMAND;
        }

        throw new \Exception("Command unrecognized");
    }

    private function prepareFileResponse ($response, FileResponse $fileResponse) {
        $fileUrl = $fileResponse->getFileUrl();
        $response = $response->withHeader('Content-Type', $fileResponse->getFileContentType())
            ->withHeader('Content-Disposition', 'attachment;filename="' . ($fileResponse->getFileName()) . '"')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate')
            ->withHeader('Pragma', 'public');

        ob_clean();
        readfile($fileUrl);
        return $response;
    }

    private function prepareDataBasedOnRequest (Request $request) {
        if ($request->isGet()) {
            return $request->getAttributes()["route"]->getArguments();
        } else {
            return $request->getParsedBody();
        }
    }
}