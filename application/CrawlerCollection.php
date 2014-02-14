<?php

use Console\Console;

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

        Console::info("Added crawler " . get_class($crawler));

        $this->crawlers[] = $crawler;

    }

    public function run()
    {

        foreach ($this->crawlers as $crawler) {

            Console::info("Running crawler " . get_class($crawler) . "... ");
            $crawler->setBaseFolder($this->base_folder);
            $crawler->run();
            Console::info("Done running crawler " . get_class($crawler));

        }

    }

    public function licences($file)
    {

        $path = $this->base_folder . '/' . $file;

        Console::info("Writing licence file to " . $path);

        $contents = '';

        foreach ($this->crawlers as $crawler) {

            $contents .= "# " . $crawler->getName() . "\n\n";
            $contents .= $crawler->getLicence()['text'] . "\n" . $crawler->getLicence()['link'] . "\n\n";
            $contents .= "---\n\n";

        }

        // FIXME file class
        file_put_contents($path, $contents);

    }

}