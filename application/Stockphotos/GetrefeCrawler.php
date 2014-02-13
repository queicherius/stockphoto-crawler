<?php namespace Stockphotos;

class GetrefeCrawler extends StockphotoCrawlerAbstract implements StockphotoCrawlerInterface
{

    private $link = 'http://getrefe.tumblr.com';
    private $folder = 'getrefe';

    public function getName()
    {
        return 'getrefe';
    }

    public function getLicence()
    {
        return [
            'text' => 'Free photos for your personal or commercial projects',
            'link' => 'http://getrefe.tumblr.com/'
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
        preg_match_all('#href="([^"]*staticflickr[^"]*)"#', $content, $array);
        return array_unique($array[1]);
    }

}