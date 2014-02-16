<?php namespace Crawler;

use Console\Console;
use File\File;

class CrawlerCollection
{

    private $crawlers;
    private $base_directory;

    public function setBaseDirectory($folder)
    {
        $this->base_directory = $folder;
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
            $crawler->setBaseDirectory($this->base_directory);
            $crawler->run();
            Console::info("Done running crawler " . get_class($crawler));

        }

    }

    public function licences($file_name)
    {

        $file = new File;
        $file->path = $this->base_directory . '/' . $file_name;

        Console::info("Writing licence file to {$file->path}");

        $licences = [];

        foreach ($this->crawlers as $crawler) {

            $name = $crawler->getName();
            $licence = $crawler->getLicence();

            $underline = str_repeat('=', strlen($name));
            $text = wordwrap($licence['text'], 80);

            $licences[] = "{$name}\n{$underline}\n\n{$text}\n{$licence['link']}";

        }

        $file->content = implode("\n\n---\n\n", $licences);
        $file->save();

    }

}