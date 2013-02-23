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
            'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider' => 'BjyAuthorize\Service\AuthenticationIdentityProviderServiceFactory',
            'BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity'   => 'BjyAuthorize\Service\AuthenticationDoctrineEntityFactory',
            'BjyAuthorize\Provider\Role\Doctrine'                           => 'BjyAuthorize\Service\DoctrineRoleProviderFactory',
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'error/403'                                       => __DIR__ . '/../view/error/403.phtml',
            'zend-developer-tools/toolbar/bjy-authorize-role' => __DIR__ . '/../view/zend-developer-tools/toolbar/bjy-authorize-role.phtml',
        ),
    ),

    'zenddevelopertools' => array(
        'profiler' => array(
            'collectors' => array(
                'bjy_authorize_role_collector' => 'BjyAuthorize\\Collector\\RoleCollector',
            ),
        ),
        'toolbar' => array(
            'entries' => array(
                'bjy_authorize_role_collector' => 'zend-developer-tools/toolbar/bjy-authorize-role',
            ),
        ),
    ),
);
