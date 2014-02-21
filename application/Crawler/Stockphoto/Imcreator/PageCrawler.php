<?php namespace Crawler\Stockphoto\Imcreator;

use Crawler\Stockphoto\StockphotoCrawler;
use Console\Console;

class PageCrawler extends StockphotoCrawler
{

    private $image;

    public function __construct($image)
    {
        $this->image = $image;
    }

    public function fetchMetadata()
    {

        $page = $this->fetchUrl($this->image['image']);

        return [
            'image'       => $this->url($page),
            'attribution' => $this->author($page),
            'cc'          => $this->licence($page),
            'subfolder'   => $this->image['subfolder']
        ];

    }

    private function url($page)
    {

        preg_match('#<a href="([^"]*)" class="download" target="_blank">#', $page, $flickr);

        if (!isset($flickr[1])) {
            Console::warning("No image found for {$this->image['image']}");
            return false;
        }

        return preg_replace('#/sizes.*$#', '', $flickr[1]);

    }

    private function author($page)
    {

        preg_match('#<a href="http://www.flickr.com/photos/([^/]*)/" target="_blank" class="author">#', $page, $author);

        if (!isset($author[1])) {
            Console::warning("No author found for {$this->image['image']}");
            return false;
        }

        return $author[1];

    }

    private function licence($page)
    {

        preg_match('#href="(http://creativecommons.org/[^"]*)"#', $page, $licence);

        if (!isset($licence[1])) {
            Console::warning("No licence found for {$this->image['image']}");
            return false;
        }

        return str_replace(['http://creativecommons.org/licenses/', '/2.0/'], '', $licence[1]);

    }

} 