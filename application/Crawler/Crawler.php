<?php namespace Crawler;

use Http\Http;
use Http\HttpException;
use File\File;
use Console\Console;

abstract class Crawler
{

    private $base_directory;

    public function setBaseDirectory($folder)
    {
        $this->base_directory = $folder;
    }

    public function getBaseDirectory()
    {
        return $this->base_directory;
    }

    protected function fetchUrl($url, $post = false)
    {

        Console::debug("Fetching url {$url}");

        $http = new Http;
        $content = false;

        try {
            $content = $http->fetch($url, $post);
        } catch (HttpException $e) {
            Console::warning("Error fetching url {$url}: {$e->getMessage()}");
        }

        return $content;

    }

    protected function saveFile($path, $content)
    {

        Console::debug("Saving file {$path}");

        $file = new File;
        $file->path = $path;
        $file->content = $content;
        $file->save();

    }

}