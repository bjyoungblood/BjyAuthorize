<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'bjyauthorize' => array(
        'default_role'          => 'guest',
        'identity_provider'     => 'BjyAuthorize\Provider\Identity\ZfcUserZendDb',
        'unauthorized_strategy' => 'BjyAuthorize\View\UnauthorizedStrategy',
        'role_providers'        => array(),
        'resource_providers'    => array(),
        'rule_providers'        => array(),
        'guards'                => array(),
        'template'              => 'error/403',
    ),
    'service_manager' => array(
        'aliases' => array(
            'bjyauthorize_zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
        ),
        'factories' => array(
            'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider' => 'BjyAuthorize\Service\AuthenticationIdentityProviderServiceFactory'
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'error/403' => __DIR__ . '/../view/error/403.phtml',
        ),
    ),
);
