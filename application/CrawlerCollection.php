<?php

class CrawlerCollection
{

    private $crawlers;
    private $base_folder;

    public function setBaseFolder($folder)
    {
        $this->base_folder = $folder;
    }

    public function add(CrawlerInterface $crawler)
    {

        Log::info("Added crawler " . get_class($crawler));

        $this->crawlers[] = $crawler;

    }

    public function run()
    {

        foreach ($this->crawlers as $crawler) {

            Log::info("Running crawler " . get_class($crawler) . "... ");
            $crawler->setBaseFolder($this->base_folder);
            $crawler->run();
            Log::info("Done running crawler " . get_class($crawler));

        }

    }

}