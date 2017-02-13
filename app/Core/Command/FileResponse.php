<?php

namespace Core\Command;

class FileResponse extends Response {
    private $fileName;
    private $fileUrl;
    private $fileContentType;

    public function __construct ($fileUrl, $fileContentType, $fileName = null) {
        $this->fileName = !empty($fileName) ? $fileName : basename($fileUrl);
        $this->fileUrl = $fileUrl;
        $this->fileContentType = $fileContentType;
    }

    public function hasFile () {
        return true;
    }

    public function getFileUrl () {
        return $this->fileUrl;
    }

    public function getFileContentType () {
        return $this->fileContentType;
    }

    public function getFileName () {
        return $this->fileName;
    }
}