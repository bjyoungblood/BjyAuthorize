<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 * @author  Marco Pivetta <ocramius@gmail.com>
 */

if (
    !($loader = @include_once __DIR__ . '/../vendor/autoload.php')
    && !@($loader = include_once __DIR__ . '/../../../autoload.php')
) {
    throw new RuntimeException('vendor/autoload.php could not be found. Did you run `php composer.phar install`?');
}

/* var $loader \Composer\Autoload\ClassLoader */
$loader->add('BjyAuthorizeTest\\', __DIR__);
