<?php namespace Crawler\Stockphoto;

class GratisographyCrawler extends StockphotoCrawler implements StockphotoCrawlerInterface
{

    private $link = 'http://www.gratisography.com';
    protected $directory = 'gratisography';

    public function getName()
    {
        return 'gratisography';
    }

    public function getLicence()
    {

        return [
            'text' => 'Using CC0, you can waive all copyrights and related or neighboring rights that you have over your work, such as your moral rights (to the extent waivable), your publicity or privacy rights, rights you have protecting against unfair competition, and database rights and rights protecting the extraction, dissemination and reuse of data.',
            'link' => 'http://creativecommons.org/choose/zero/'
        ];

    }

    public function run()
    {

        $content = $this->fetchUrl($this->link);
        $image_links = $this->getImageLinks($content);

        $this->downloadImages($image_links);

    }

    private function getImageLinks($content)
    {

        preg_match_all('#pictures/[0-9]*H.jpg#', $content, $array);

        $array = $array[0];

        array_walk(
            $array,
            function (&$value) {
                $value = $this->link . '/' . $value;
            }
        );

        return array_unique($array);

    }

}