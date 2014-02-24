<?php namespace Crawler\Stockphoto;

use Crawler\Crawler;
use Console\Console;
use Console\Progressbar;
use File\File;

abstract class StockphotoCrawler extends Crawler
{

    private $file_types = ['jpg', 'jpeg', 'gif', 'png'];
    private $default_file_type = 'jpg';
    protected $directory = 'default';

    public function downloadImages($images)
    {

        $downloadable_images = count($images);

        $images = $this->cleanArray($images);
        $images = $this->imagesWithMetadata($images);
        $images = $this->filterExistingFiles($images);

        $new_images = count($images);

        $skipped_images = $downloadable_images - $new_images;

        Console::info("Downloading {$new_images} images ({$skipped_images} skipped)...");

        $progess = new Progressbar($new_images);

        foreach ($images as $image) {

            $this->downloadImage($image);
            $progess->increase();

        }

    }

    private function imagesWithMetadata($images)
    {

        array_walk(
            $images,
            function (&$image) {

                $image = $this->imageWithMetadata($image);

            }
        );

        return $images;

    }

    private function imageWithMetadata($image)
    {

        $defaults = [
            'attribution' => false,
            'subfolder'   => false,
            'cc'          => false
        ];

        $image_metadata = [];

        if (!is_array($image)) {
            $image_metadata['image'] = $image;
        } else {
            $image_metadata = $image;
        }

        return array_merge($defaults, $image_metadata);

    }

    private function filterExistingFiles($images)
    {

        return array_filter(
            $images,
            function ($image) {

                $file = new File();
                $file->path = $this->getFilePath($image);
                return !$file->exists();

            }
        );

    }

    public function downloadImage($image)
    {

        $path = $this->getFilePath($image);
        $contents = $this->fetchUrl($image['image']);

        if ($contents) {
            $this->saveFile($path, $contents);
        }

    }

    private function getFilePath($image)
    {

        $url = $image['image'];
        $attribution = ($image['attribution']) ? '-by-' . $image['attribution'] : '';
        $subfolder = ($image['subfolder']) ? $image['subfolder'] . '/' : '';
        $licence = ($image['cc']) ? '-cc-' . $image['cc'] : '';

        return $this->getBaseDirectory() . '/' . $this->directory . '/' . $subfolder .
        md5($url) . $attribution . $licence . '.' . $this->getFileType($url);

    }

    private function getFileType($url)
    {

        $file_extension = pathinfo($url, PATHINFO_EXTENSION);
        return (in_array($file_extension, $this->file_types)) ? $file_extension : $this->default_file_type;

    }

} 