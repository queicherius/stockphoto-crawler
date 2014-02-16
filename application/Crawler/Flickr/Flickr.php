<?php namespace Crawler\Flickr;

use Crawler\Crawler;
use Console\Console;
use Console\Progressbar;

class Flickr extends Crawler
{

    public function getOriginalImageUrls($images)
    {

        $original_images = [];

        Console::info("Getting original image urls for " . count($images) . " flickr links...");
        $progress = new Progressbar(count($images));

        foreach ($images as $url) {

            $original_url = $this->getOriginalImageUrl($url);

            if ($original_url) {
                $original_images[] = $original_url;
            }

            $progress->increase();

        }

        return array_unique($original_images);

    }

    public function getOriginalImageUrl($url)
    {

        $url = $this->getSizesUrl($url);

        $contents = $this->fetchUrl($url, false, true);
        $original_url = $this->findOriginalImage($contents);

        if ($original_url === false) {
            Console::notice('No flickr image found for ' . $url);
        }

        return $original_url;

    }

    private function getSizesUrl($url)
    {
        return trim($url, '/') . '/sizes/o';
    }

    private function findOriginalImage($content)
    {

        preg_match('#<img src="(http://[^\.]*.staticflickr.[^"]*)">#', $content, $matches);

        if (!isset($matches[1])) {
            return false;
        }

        return $matches[1];

    }

}