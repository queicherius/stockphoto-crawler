<?php namespace Stockphotos;

use Log;

class UnsplashCrawler extends StockphotoCrawlerAbstract implements StockphotoCrawlerInterface
{

    private $link = 'http://unsplash.com';
    private $folder = 'unsplash';

    public function getName()
    {
        return 'unsplash';
    }

    public function getLicence()
    {
        return [
            'text' => "The person who associated a work with this deed has dedicated the work to the public domain by waiving all of his or her rights to the work worldwide under copyright law, including all related and neighboring rights, to the extent allowed by law.\nYou can copy, modify, distribute and perform the work, even for commercial purposes, all without asking permission.",
            'link' => 'http://creativecommons.org/publicdomain/zero/1.0/'
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
        $this->downloadImages($image_links, $this->folder);

    }

    private function getPage($number)
    {
        return $this->getUrlContents($this->link . '/page/' . $number);
    }

    private function getImageLinks($content)
    {
        preg_match_all('#http://bit\.ly/[0-9a-zA-Z]*#', $content, $array);
        return array_unique($array[0]);
    }

}