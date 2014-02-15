<?php namespace Crawler;

interface CrawlerInterface
{
    public function run();

    public function setBaseFolder($folder);

    public function getName();

    public function getLicence();
}