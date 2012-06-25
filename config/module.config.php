<?php

return array(
    'bjyauthorize' => array(
        'default_role' => 'guest',
        'identity_provider' => 'BjyAuthorize\Provider\Identity\ZfcUserZendDb',
        'role_providers' => array(
        ),
        'resource_providers' => array(
        ),
        'rule_providers' => array(
        ),
        'guards' => array(
        ),
    ),

    'controller' => array(
        'map' => array(
            'isallowed' => 'BjyAuthorize\Controller\Plugin\IsAllowed',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(__DIR__ . '/../view'),
        'helper_map' => array(
            'isallowed' => 'BjyAuthorize\View\Helper\IsAllowed',
        ),
    ),
);
