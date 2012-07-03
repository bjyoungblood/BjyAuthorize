<?php
/**
 * User:dsamblas
 * Date: 03/07/12
 * Time: 12:00
 */
return function ($class) {
    static $map;
    if (!$map) {
        $map = include __DIR__ . '/autoload_classmap.php';
    }

    if (!isset($map[$class])) {
        return false;
    }
    return include $map[$class];
};
