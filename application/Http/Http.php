<?php namespace Http;

class Http
{

    public function fetch($url, $post_data = false)
    {

        if ($post_data) {
            $content = $this->post($url, $post_data);
        } else {
            $content = $this->get($url);
        }

        if (!$content) {
            throw new HttpException("{$this->getLastError()}");
        }

        return $content;

    }

    public function get($url)
    {
        return @file_get_contents($url);
    }

    public function post($url, $post_data)
    {

        $stream_context = $this->streamContext($post_data);
        return @file_get_contents($url, false, $stream_context);

    }

    private function streamContext($post)
    {

        $post = http_build_query($post);
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post
            ]
        ];

        return stream_context_create($options);

    }

    private function getLastError()
    {
        $error_message = error_get_last()['message'];

        // Remove url
        $error_message = preg_replace('#file_get_contents\([^\)]*\): #', '', $error_message);

        // Remove everything up to the HTTP error
        $error_message = preg_replace('#^.*([0-9]{3})#', '$1', $error_message);

        return $error_message;
    }

}