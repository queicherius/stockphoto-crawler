<?php namespace File;

class File
{

    public $path;
    public $content;

    public function save()
    {

        if (!$this->directoryExists()) {
            $this->createDirectory();
        }

        $this->write();

    }

    private function directoryExists()
    {
        return is_dir(dirname($this->path));
    }

    private function createDirectory()
    {
        return mkdir(dirname($this->path), 0, true);
    }

    private function write()
    {
        file_put_contents($this->path, $this->content);
    }

    public function read()
    {

        if (!$this->exists()) {
            throw new FileException("File {$this->path} doesn't exist");
        }

        $this->content = file_get_contents($this->path);
        return $this->content;

    }

    public function exists()
    {
        return is_file($this->path);
    }

} 