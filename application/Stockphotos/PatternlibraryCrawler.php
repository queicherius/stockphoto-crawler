<?php namespace Stockphotos;

class PatternlibraryCrawler extends StockphotoCrawlerAbstract implements StockphotoCrawlerInterface
{

    private $link = 'http://thepatternlibrary.com/js/pattern-library.js';
    private $folder = 'patternlibrary';

    public function getName()
    {
        return 'patternlibrary';
    }

    public function getLicence()
    {
        return [
            'text' => "TO USE FREELY IN YOUR DESIGNS",
            'link' => 'http://thepatternlibrary.com/'
        ];
    }

    public function run()
    {

        $content = $this->getUrlContents($this->link);
        $image_links = $this->getImageLinks($content);

        $this->downloadImages($image_links, $this->folder);

    }

    private function getImageLinks($content)
    {
        preg_match_all('#file: \'([^\']*)\'#', $content, $array);

        $array = $array[1];

        array_walk(
            $array,
            function (&$value) {
                $value = 'http://thepatternlibrary.com/img/' . $value;
            }
        );

        return array_unique($array);
    }

}