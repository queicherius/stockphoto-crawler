<?php namespace Stockphotos;

use CrawlerAbstract;
use Log;

abstract class StockphotoCrawlerAbstract extends CrawlerAbstract
{

    public function downloadImages($images, $folder, $file_type = 'jpg')
    {

        $count = count($images);
        $images = $this->filterExistingFiles($images, $folder, $file_type);

        $skiped_images = $count - count($images);

        Log::info('Downloading ' . count($images) . ' images (' . $skiped_images . ' skiped)...');

        foreach ($images as $image) {
            $this->downloadImage($image, $folder, $file_type);
        }

    }

    public function downloadImage($url, $folder, $file_type = 'jpg')
    {

        $path = $this->getFullPath($url, $folder, $file_type);
        $contents = $this->getUrlContents($url);

        if ($contents) {
            $this->saveFile($path, $contents);
        }

    }

    private function filterExistingFiles($images, $folder, $file_type)
    {

        return array_filter(
            $images,
            function ($image) use ($folder, $file_type) {
                $path = $this->getFullPath($image, $folder, $file_type);
                return !is_file($path);
            }
        );

    }

    private function getFullPath($url, $folder, $file_type)
    {
        return $this->getBaseFolder() . '/' . $folder . '/' . md5($url) . '.' . $file_type;
    }

} 