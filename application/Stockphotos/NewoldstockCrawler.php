<?php namespace Stockphotos;

use Flickr;

class NewoldstockCrawler extends StockphotoCrawlerAbstract implements StockphotoCrawlerInterface
{

    private $link = 'http://nos.twnsnd.co';
    private $folder = 'newoldstock';

    public function getName()
    {
        return 'newoldstock';
    }

    public function getLicence()
    {
        return [
            'text' => "No known copyright restrictions.",
            'link' => 'http://www.flickr.com/commons/usage/'
        ];
    }

    public function run()
    {

        $page = 1;
        $image_links = [];

        do {

            $content = $this->getPage($page);
            $page_links = $this->getImageLinks($content);

            $image_links = array_merge($image_links, $page_links);

            $page++;

        } while (count($page_links) > 0);

        $image_links = array_unique($image_links);

        $flickr = new Flickr;
        $image_links = $flickr->getOriginalImageUrls($image_links);

        $this->downloadImages($image_links, $this->folder);

    }

    private function getPage($number)
    {
        return $this->fetchUrl($this->link . '/page/' . $number);
    }

    private function getImageLinks($content)
    {
        preg_match_all('#http://flic\.kr/[^"]*#', $content, $array);

        return array_unique($array[0]);
    }

}