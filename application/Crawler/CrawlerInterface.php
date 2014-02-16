<?php namespace Crawler;

interface CrawlerInterface
{
    public function run();

    public function setBaseDirectory($folder);

    public function getName();

    public function getLicence();
}