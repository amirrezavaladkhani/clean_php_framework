<?php

if (!function_exists('config')) {
    function config($key, $default = null)
    {
        static $config = [];

        if (empty($config)) {
            foreach (glob(__DIR__ . '/../../config/*.php') as $file) {
                $name = basename($file, '.php');
                $config[$name] = require $file;
            }
        }

        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return $default;
            }
            $value = $value[$key];
        }

        return $value;
    }

    if (!function_exists('now')) {
        function now(): string
        {
            return date('Y-m-d H:i:s');
        }
    }
}
