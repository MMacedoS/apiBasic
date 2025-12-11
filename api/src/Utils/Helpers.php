<?php

if (!function_exists('dd')) {
    function dd(...$params)
    {
        echo '<pre>';
        foreach ($params as $param) {
            var_dump($param);
        }
        echo '</pre>';
        die();
    }
}
