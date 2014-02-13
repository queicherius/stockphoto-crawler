<?php namespace Stockphotos;

class PicjumboCrawler extends StockphotoCrawlerAbstract implements StockphotoCrawlerInterface
{

    private $link = 'http://picjumbo.com/';
    private $folder = 'picjumbo';

    public function getName()
    {
        return 'picjumbo';
    }

    public function getLicence()
    {
        return [
            'text' => "Totally free photos for your commercial & personal works",
            'link' => 'http://picjumbo.com/'
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

        http: //picjumbo.com/wp-content/uploads/IMG_3053-1300x866.jpg
        preg_match_all('#http://picjumbo\.picjumbocom\.netdna-cdn[^"]*IMG_[\d]*[^"]*#', $content, $array);

        $array = $array[0];

        array_walk(
            $array,
            function (&$value) {
                $value = preg_replace('#-[^\/\.]*(\.[\w]*)$#', '$1', $value);
            }
        );

        return array_unique($array);
    }

}