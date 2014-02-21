<?php namespace Crawler\Stockphoto;

use Console\Console;
use Console\Progressbar;
use Crawler\Flickr\Flickr;
use Crawler\Stockphoto\Imcreator\PageCrawler;

class ImcreatorCrawler extends StockphotoCrawler implements StockphotoCrawlerInterface
{

    private $link = 'http://imcreator.com/index.php';
    private $categories = [
        118 => 'ambient',
        163 => 'arts-and-music',
        26  => 'business',
        149 => 'bw',
        125 => 'cityscape',
        60  => 'education',
        84  => 'fashion-and-beauty',
        50  => 'food-and-drinks',
        148 => 'inspiration',
        164 => 'lifestyles',
        88  => 'nature',
        162 => 'objects-and-items',
        151 => 'occupations',
        21  => 'people',
        116 => 'recreation',
        55  => 'sports-and-fitness',
        28  => 'technology',
        121 => 'transportation'
    ];
    protected $directory = 'imcreator';

    public function getName()
    {
        return 'imcreator';
    }

    public function getLicence()
    {

        return [
            'text' => 'Please follow the licence information in the images. You can check the licence by taking the part after -cc- and inserting it into this url: http://creativecommons.org/licenses/INSERTHERE/2.0/, for example http://creativecommons.org/licenses/by/2.0/',
            'link' => ""
        ];

    }

    public function run()
    {

        $counter = 0;
        $count = count($this->categories);

        foreach ($this->categories as $category_id => $category_name) {

            // Getting the categories one by one and not all images of the categories because
            // else the server is blocking (too many requests on one url)
            Console::info("Fetching category " . ++$counter . " of " . $count . " (" . $category_name . ")... ");
            $this->fetchCategory($category_id, $category_name);

        }

    }

    private function fetchCategory($id, $name)
    {
        $category_links = $this->getCategoryLinks($id);
        $category_links = $this->imagesWithMetadata($category_links, $name);
        $category_links = $this->imagesWithFlickrUrls($category_links);
        $this->downloadImages($category_links);
    }

    private function getCategoryLinks($category_id)
    {

        $page = 0;
        $image_links = [];

        do {

            $content = $this->getPage($page, $category_id);
            $page_links = $this->getImageLinks($content);

            $image_links = array_merge($image_links, $page_links);

            $page++;

        } while (count($page_links) > 0);

        return array_unique($image_links);

    }

    private function getPage($number, $category_id)
    {

        $form_data = [
            'ajax_load_img' => 'load',
            'm_cat_id'      => $category_id,
            'page'          => $number
        ];

        $content = $this->fetchUrl($this->link, $form_data);

        return $content;

    }

    private function getImageLinks($content)
    {

        preg_match_all('#<a href="(http://imcreator\.com/free/[^"]*)"#', $content, $array);
        preg_match_all('#<a href="(http://imcreator\.com/images/[^"]*)"#', $content, $array2);

        return array_unique(array_merge($array[1], $array2[1]));

    }

    private function imagesWithMetadata($images, $category_name)
    {

        Console::info('Getting attribution, licence and flickr links for ' . count($images) . ' images...');

        $progress = new Progressbar(count($images));

        array_walk(
            $images,
            function (&$image) use ($category_name, $progress) {

                // Transform the image links into an array
                $image = [
                    'image'       => $image,
                    'attribution' => 'NOT_DEFINED',
                    'subfolder'   => $category_name
                ];

                // Fetch metadata
                $page_crawer = new PageCrawler($image);
                $image = $page_crawer->fetchMetadata();

                // Update progressbar
                $progress->increase();

            }
        );

        return $images;

    }

    private function imagesWithFlickrUrls($images)
    {

        Console::info("Getting original image urls for " . count($images) . " flickr links...");
        $progress = new Progressbar(count($images));

        $flickr = new Flickr();

        array_walk(
            $images,
            function (&$value) use ($progress, $flickr) {

                $original_image = $flickr->getOriginalImageUrl($value['image']);

                if (!$original_image) {
                    unset($value);
                }

                $value['image'] = $original_image;
                $progress->increase();

            }
        );

        return $images;

    }

}