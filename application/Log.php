<?php

class Log
{

    private static $colors = [
        'INFO'    => '32',
        'DEBUG'   => '35',
        'WARNING' => '31'
    ];

    public static $disabled = [];

    public static function __callStatic($type, $arguments)
    {

        $type = strtoupper($type);
        $message = trim($arguments[0], "    \n");

        if (in_array($type, self::$disabled)) {
            return false;
        }

        echo self::preText($type) . $message . "\n";
    }

    private static function preText($type)
    {

        $date = '[' . self::dateString() . ']';
        $type = self::colorString($type, '[' . $type . ']');

        return $date . $type . ' ';
    }

    private static function colorString($type, $string)
    {

        if (!isset(self::$colors[$type])) {
            return $string;
        }

        return "\033[" . self::$colors[$type] . "m" . $string . "\033[0m";
    }

    private static function dateString()
    {
        return date('H:i:s');
    }

}