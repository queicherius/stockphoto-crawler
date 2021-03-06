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

    protected function fetchUrl($url, $post = false, $no_warnings = false)
    {

        Console::debug("Fetching url {$url}");

        $http = new Http;
        $content = false;

        try {
            $content = $http->fetch($url, $post);
        } catch (HttpException $e) {
            if (!$no_warnings) {
                Console::warning("Error fetching url {$url}: {$e->getMessage()}");
            }
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

    protected function cleanArray($array)
    {

        return array_filter(
            $array,
            function ($value) {

                if (is_array($value)) {
                    return !($value['image'] === false);
                }

                return !($value === false);

            }
        );

    }

}