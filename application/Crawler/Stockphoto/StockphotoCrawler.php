<?php namespace Crawler\Stockphoto;

use Crawler\Crawler;
use Console\Console;
use Console\Progressbar;

abstract class StockphotoCrawler extends Crawler
{

    private static $file_types = ['jpg', 'jpeg', 'gif', 'png'];
    private static $default_file_type = 'jpg';

    public function downloadImages($images, $folder)
    {

        $count = count($images);
        $images = $this->filterExistingFiles($images, $folder);

        $skiped_images = $count - count($images);

        Console::info('Downloading ' . count($images) . ' images (' . $skiped_images . ' skiped)...');

        $progess = new Progressbar();

        $counter = 0;

        foreach ($images as $image) {

            $percent = (++$counter / count($images)) * 100;

            $attribution = false;

            if (is_array($image)) {
                $attribution = $image['attribution'];
                $image = $image['image'];
            }

            $this->downloadImage($image, $folder, $attribution);
            $progess->setProgress($percent);

        }

    }

    public function downloadImage($url, $folder, $attribution = false)
    {

        $path = $this->getFullPath($url, $folder, $attribution);
        $contents = $this->fetchUrl($url);

        if ($contents) {
            $this->saveFile($path, $contents);
        }

    }

    private function filterExistingFiles($images, $folder)
    {

        return array_filter(
            $images,
            function ($image) use ($folder) {

                $attribution = false;

                if (is_array($image)) {
                    $attribution = $image['attribution'];
                    $image = $image['image'];
                }

                $path = $this->getFullPath($image, $folder, $attribution);
                return !is_file($path);
            }
        );

    }

    private function getFullPath($url, $folder, $attribution)
    {

        if ($attribution) {
            $attribution = '-by-' . $attribution;
        } else {
            $attribution = '';
        }

        return $this->getBaseDirectory() . '/' . $folder . '/' . md5($url) . $attribution . '.' . $this->getFileType($url);

    }

    private function getFileType($url)
    {

        $file_extension = pathinfo($url, PATHINFO_EXTENSION);
        return (in_array($file_extension, self::$file_types)) ? $file_extension : self::$default_file_type;

    }

} 