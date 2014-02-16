<?php namespace Http;

class Http
{

    private $curl_handle;

    public function fetch($url, $post_data = false)
    {

        $this->initializeRequest($url);

        if ($post_data) {
            $this->setPostData($post_data);
        }

        $content = $this->executeRequest();

        if ($this->hasError()) {
            throw new HttpException("{$this->getErrorMessage()}");
        }

        $this->closeRequest();

        return $content;

    }

    private function initializeRequest($url)
    {

        $this->curl_handle = curl_init();

        curl_setopt_array(
            $this->curl_handle,
            [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false
            ]
        );

    }

    private function setPostData($data)
    {

        curl_setopt_array(
            $this->curl_handle,
            [
                CURLOPT_POST       => 1,
                CURLOPT_POSTFIELDS => http_build_query($data)
            ]
        );

    }

    private function executeRequest()
    {
        return curl_exec($this->curl_handle);
    }

    private function closeRequest()
    {
        curl_close($this->curl_handle);
    }

    private function hasError()
    {

        // Curl error
        if (curl_error($this->curl_handle)) {
            return true;
        }

        // Http error
        if (curl_getinfo($this->curl_handle, CURLINFO_HTTP_CODE) !== 200) {
            return true;
        }

        return false;

    }

    private function getErrorMessage()
    {

        // Curl error
        if (curl_error($this->curl_handle)) {
            return curl_error($this->curl_handle);
        }

        // Http error
        if (curl_getinfo($this->curl_handle, CURLINFO_HTTP_CODE) !== 200) {
            return $this->httpErrorMessage(curl_getinfo($this->curl_handle, CURLINFO_HTTP_CODE));
        }

    }

    private function httpErrorMessage($error_code)
    {

        switch ($error_code) {
            case 404:
                return '404 not found';
            case 503:
                return '503 service unavailable';
            case 509:
                return '509 bandwidth limit exceeded';
            default:
                return 'HTTP ' . $error_code;
        }

    }

}