<?php namespace Crawler\Stockphoto;

use Crawler\Crawler;
use Console\Console;
use Console\Progressbar;
use File\File;

abstract class StockphotoCrawler extends Crawler
{

    private static $file_types = ['jpg', 'jpeg', 'gif', 'png'];
    private static $default_file_type = 'jpg';
    protected $directory = 'default';

    public function downloadImages($images)
    {

        $downloadable_images = count($images);

        $images = $this->imagesWithAttribution($images);
        $images = $this->filterExistingFiles($images);

        $new_images = count($images);

        $skipped_images = $downloadable_images - $new_images;

        Console::info('Downloading ' . $new_images . ' images (' . $skipped_images . ' skipped)...');

        $progess = new Progressbar($new_images);

        foreach ($images as $image) {

            $this->downloadImage($image['image'], $image['attribution']);
            $progess->increase();

        }

    }

    private function imagesWithAttribution($images)
    {

        array_walk(
            $images,
            function (&$image) {

                if (!is_array($image)) {

                    $image = [
                        'image'       => $image,
                        'attribution' => false
                    ];

                }

            }
        );

        return $images;

    }

    private function filterExistingFiles($images)
    {

        return array_filter(
            $images,
            function ($image) {

                $file = new File();
                $file->path = $this->getFilePath($image['image'], $image['attribution']);
                return !$file->exists();

            }
        );

    }

    public function downloadImage($url, $attribution = false)
    {

        $path = $this->getFilePath($url, $attribution);
        $contents = $this->fetchUrl($url);

        if ($contents) {
            $this->saveFile($path, $contents);
        }

    }

    private function getFilePath($url, $attribution)
    {

        $attribution = ($attribution) ? '-by-' . $attribution : '';

        return $this->getBaseDirectory() . '/' . $this->directory . '/' .
        md5($url) . $attribution . '.' . $this->getFileType($url);

    }

    private function getFileType($url)
    {

        $file_extension = pathinfo($url, PATHINFO_EXTENSION);
        return (in_array($file_extension, self::$file_types)) ? $file_extension : self::$default_file_type;

    }

} 