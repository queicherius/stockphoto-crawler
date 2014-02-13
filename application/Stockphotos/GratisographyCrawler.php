<?php namespace Stockphotos;

class GratisographyCrawler extends StockphotoCrawlerAbstract implements StockphotoCrawlerInterface
{

    private $link = 'http://www.gratisography.com';
    private $folder = 'gratisography';

    public function run()
    {

        $content = $this->getUrlContents($this->link);
        $image_links = $this->getImageLinks($content);

        $image_links = array_unique($image_links);
        $this->downloadImages($image_links, $this->folder);

    }

    private function getImageLinks($content)
    {
        preg_match_all('#pictures/[0-9]*H.jpg#', $content, $array);

        $array = $array[0];

        array_walk($array, function (&$value) {
            $value = $this->link . '/' . $value;
        });

        return array_unique($array);
    }

}