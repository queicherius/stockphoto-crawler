<?php

spl_autoload_register(
    function ($class) {

        $path = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';

        if (file_exists($path)) {
            require $path;
            return true;
        }

        return false;

    }
);