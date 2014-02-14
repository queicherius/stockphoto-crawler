<?php

use Http\Http;
use File\File;

abstract class CrawlerAbstract
{

    private $base_folder;

    public function setBaseFolder($folder)
    {
        $this->base_folder = $folder;
    }

    public function getBaseFolder()
    {
        return $this->base_folder;
    }

    protected function fetchUrl($url, $post = false)
    {

        Log::debug("Fetching url {$url}");

        $http = new Http;
        $content = false;

        try {
            $content = $http->fetch($url, $post);
        } catch (Exception $e) {
            Log::warning("Error fetching url {$url}: {$e->getMessage()}");
        }

        return $content;
    }

    protected function saveFile($path, $content)
    {

        Log::debug("Saving file {$this->path}");

        $file = new File;
        $file->path = $path;
        $file->content = $content;
        $file->save();
    }

}