<?php namespace Crawler\Stockphoto;

use Console\Console;
use Console\Progressbar;
use Crawler\Stockphoto\Splitshire\PageCrawler;

class SplitshireCrawler extends StockphotoCrawler implements StockphotoCrawlerInterface
{

    private $link = 'http://splitshire.com';
    protected $directory = 'splitshire';

    public function getName()
    {
        return 'splitshire';
    }

    public function getLicence()
    {

        return [
            'text' => 'I wanna share with you my photos for free!!! Do what you want with them, for personal and commercial use. Yes, everything free of charge, just an attribution if you like my work or offer me a coffe through donation to help me mainting this site free for you.',
            'link' => 'http://splitshire.com/'
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
        $image_links = $this->postImages($image_links);

        $this->downloadImages($image_links);

    }

    private function getPage($number)
    {
        return $this->fetchUrl($this->link . '/page/' . $number, false, true);
    }

    private function getImageLinks($content)
    {

        preg_match_all('#<a href="([^"]*)" class="more-link">Free Download</a>#', $content, $array);
        return array_unique($array[1]);

    }

    private function postImages($images)
    {

        Console::info('Getting image links for ' . count($images) . ' posts...');

        $progress = new Progressbar(count($images));

        array_walk(
            $images,
            function (&$image) use ($progress) {

                // Fetch metadata
                $page_crawer = new PageCrawler($image);
                $image = $page_crawer->fetch();

                // Update progressbar
                $progress->increase();

            }
        );

        return $images;

    }

}