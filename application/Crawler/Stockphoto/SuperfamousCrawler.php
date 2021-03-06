<?php namespace Crawler\Stockphoto;

use Crawler\Flickr\Flickr;

class SuperfamousCrawler extends StockphotoCrawler implements StockphotoCrawlerInterface
{

    private $link = 'http://superfamous.com/designs/fullfeed/main-pagination.php';
    protected $directory = 'superfamous';

    public function getName()
    {
        return 'superfamous';
    }

    public function getLicence()
    {

        return [
            'text' => "Attribution — You must give appropriate credit, provide a link to the license, and indicate if changes were made. You may do so in any reasonable manner, but not in any way that suggests the licensor endorses you or your use.",
            'link' => 'http://creativecommons.org/licenses/by/3.0/'
        ];

    }

    public function run()
    {

        $page = 0;
        $image_links = [];

        do {

            $content = $this->getPage($page);
            $page_links = $this->getImageLinks($content);

            $image_links = array_merge($image_links, $page_links);

            $page++;

        } while (count($page_links) > 0);

        $image_links = array_unique($image_links);

        $flickr = new Flickr();
        $image_links = $flickr->getOriginalImageUrls($image_links);

        array_walk(
            $image_links,
            function (&$value) {
                $value = ['image' => $value, 'attribution' => 'superfamous'];
            }
        );

        $this->downloadImages($image_links);

    }

    private function getPage($number)
    {

        $form_data = [
            'should_paginate'  => true,
            'is_updating'      => true,
            'within_bounds'    => true,
            'preload_distance' => 1500,
            'page'             => $number,
            'more_load_handle' => '#moreload',
            'ajax_route'       => '../designs/fullfeed/main-pagination.php',
            'is_ajax'          => true,
            'height_selector'  => '#content_container',
            'limit'            => 24,
            'offset'           => $number * 24,
            'url'              => 'superfamous',
            'cat'              => null
        ];
        $content = $this->fetchUrl($this->link, $form_data);

        $content = json_decode($content)->html;

        return $content;

    }

    private function getImageLinks($content)
    {

        preg_match_all('#http://www\.flickr\.com/photos/superfamous/[^\/\"]*#', $content, $array);
        return array_unique($array[0]);

    }

}