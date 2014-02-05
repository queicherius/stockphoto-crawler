<?php

interface CrawlerInterface
{
    public function run();

    public function setBaseFolder($folder);
}