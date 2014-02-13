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

    protected function getUrlContents($url, $post = false)
    {

        Log::debug('Fetching url ' . $url);

        if ($post) {
            $stream_context = $this->createStreamContext($post);
            $contents = @file_get_contents($url, false, $stream_context);
        } else {
            $contents = @file_get_contents($url);
        }

        if ($contents === false) {
            Log::warning('Error occured when fetching url ' . $url . ': ' . $this->getLastError($url));
            return false;
        }

        return $contents;

    }

    private function createStreamContext($post)
    {

        $post = http_build_query($post);
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post
            ]
        ];

        return stream_context_create($options);

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