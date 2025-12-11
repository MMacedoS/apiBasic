<?php

if (function_exists('dd')) {
    function dd(...$args)
    {
        foreach ($args as $arg) {
            echo '<pre>';
            var_dump($arg);
            echo '</pre>';
        }
        exit(1);
    }
}
