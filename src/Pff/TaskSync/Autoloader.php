<?php

namespace Pff\TaskSync;

class Autoloader
{
    public static function register()
    {
        spl_autoload_register(array('Pff\TaskSync\Autoloader', 'autoload'));
    }
    public static function autoload($class_name)
    {
        $base_path = dirname(__FILE__).'/../..';
        $rel_path = str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $class_name).'.php';
        $full_path = $base_path.DIRECTORY_SEPARATOR.$rel_path;
        if (file_exists($full_path))
        {
            require_once $full_path;
        }
    }
}