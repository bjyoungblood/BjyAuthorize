<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 * @author  Marco Pivetta <ocramius@gmail.com>
 */

return array(
    'modules' => array(
        'ZfcBase',
        'ZfcUser',
        'BjyAuthorize'
    ),
    'module_listener_options' => array(
        'config_glob_paths' => array(
            __DIR__ . '/testing.config.php',
        ),
        'module_paths' => array(),
    ),
);
