<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'doctrine' => array(
        'driver' => array(
            'bjyauthorize_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\XmlDriver',
                'paths' => __DIR__ . '/../../vendor/bjyoungblood/BjyAuthorize/config/xml/doctrine-entities',
            ),
            'orm_default' => array(
                'drivers' => array(
                    'BjyAuthorize\Entity'  => 'bjyauthorize_entity'
                )
            )
        )
    ),
);