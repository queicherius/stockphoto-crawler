<?php

class Flickr extends CrawlerAbstract
{

    public function getOriginalImageUrls($images)
    {
        $original_images = [];

        Log::info("Getting original image urls for " . count($images) . " flickr links...");
        $progress = new Log\Progressbar;

        $counter = 0;

        foreach ($images as $url) {

            $percent = (++$counter / count($images)) * 100;

            $original_images[] = $this->getOriginalImageUrl($url);

            $progress->setProgress($percent);

        }

        return array_unique($original_images);
    }

    public function getOriginalImageUrl($url)
    {

        $url = $this->getSizesUrl($url);

        $contents = $this->fetchUrl($url);
        $url = $this->findOriginalImage($contents);

        if ($url === false) {
            Log::warning('No flickr image found on url ' . $url);
        }

        return $url;
    }

    private function getSizesUrl($url)
    {
        $url = trim($url, '/');
        return $url . '/sizes/o';
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