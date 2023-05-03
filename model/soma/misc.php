<?php

/*
 * Find a variable by its name in an array
 * Example usage: $is_post = from($_REQUEST, 'is_post');
 */

function from(array $source, string $name) {
    if (is_array($name)) {
        $data = array();
        foreach ($name as $k) {
            $data[$k] = isset($source[$k]) ? $source[$k] : null;
        }
        return $data;
    }
    return isset($source[$name]) ? $source[$name] : null;
}

/*
 * Returns the value for some given key in config.ini
 * For example: $view_root = config('views.root');
 */
function config($key, $value = null) {
    static $_config = array();

    if (($key === 'source') && (isset($value)) && file_exists($value)) {
        // The 'source' config is in a file
        $_config = parse_ini_file($value, true);
    }
    // The 'source' config is already in memory
    elseif ($value == null) {
        // If it is already available, just read the value of key in memory
        if (isset($_config[$key])) {
            return $_config[$key];
        } else {
            // If it is not available, read the value of key from file
            $value = 'config/config.ini';
//            echo getcwd() ;
            $_config = parse_ini_file($value, true);
        }
    } else {
        // Change the value of the key in memory
        $_config[$key] = $value;
    }
}

