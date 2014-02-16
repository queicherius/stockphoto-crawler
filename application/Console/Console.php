<?php namespace Console;

class Console
{

    private static $colors = [
        'INFO'    => '32',
        'DEBUG'   => '35',
        'WARNING' => '31',
        'NOTICE'  => '33'
    ];

    public static $disabled = [];

    public static function __callStatic($type, $arguments)
    {

        $type = strtoupper($type);
        $message = trim($arguments[0], "    \n");
        $end_of_line = (isset($arguments[1])) ? $arguments[1] : "\n";

        if (in_array($type, self::$disabled)) {
            return false;
        }

        echo self::heading($type) . $message . $end_of_line;

    }

    private static function heading($type)
    {

        $date = '[' . self::dateString() . ']';
        $type = self::typeColor($type, '[' . $type . ']');

        return $date . $type . ' ';

    }

    private static function typeColor($type, $string)
    {

        if (!isset(self::$colors[$type])) {
            return $string;
        }

        return self::colorString(self::$colors[$type], $string);

    }

    public static function colorString($color, $string)
    {
        return "\033[" . $color . "m" . $string . "\033[0m";
    }

    private static function dateString()
    {
        return date('H:i:s');
    }

}