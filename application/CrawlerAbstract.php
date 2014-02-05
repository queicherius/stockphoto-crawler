<?php

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

    protected function getUrlContents($url)
    {

        Log::debug('Fetching url ' . $url);

        $contents = @file_get_contents($url);

        if ($contents === false) {
            Log::warning('Error occured when fetching url ' . $url . ': ' . $this->getLastError($url));
            return false;
        }

        return $contents;

    }

    private function getLastError($url)
    {
        $error_message = error_get_last()['message'];
        $error_message = str_replace('file_get_contents(' . $url . '): ', '', $error_message);

        // Remove everything up to the HTTP error
        $error_message = preg_replace('#^.*([0-9]{3})#', '$1', $error_message);

        return $error_message;
    }

    protected function saveFile($path, $contents)
    {

        $this->createDirectoryStructure($path);

        Log::debug('Saving file ' . $path);

        file_put_contents($path, $contents);

    }

    private function createDirectoryStructure($path)
    {

        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0, true);
        }
    }

}