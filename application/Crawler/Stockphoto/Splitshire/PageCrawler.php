<?php namespace Crawler\Stockphoto\Splitshire;

use Crawler\Stockphoto\StockphotoCrawler;
use Console\Console;

class PageCrawler extends StockphotoCrawler
{

    private $image;

    public function __construct($image)
    {
        $this->image = $image;
    }

    public function fetch()
    {

        $page = $this->fetchUrl($this->image);
        return $this->getDownload($page);

    }

    private function getDownload($page)
    {

        preg_match('#href="(http://splitshire\.com/\?ddownload=[\d]*)"#', $page, $array);

        if (!isset($array[1])) {
            Console::notice("No image found for {$this->image}");
            return false;
        }

        return $array[1];

    }

} 