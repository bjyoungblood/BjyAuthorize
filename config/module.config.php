<?php

return array(
    'bjyauthorize' => array(
        'default_role' => 'guest',
        'identity_provider' => 'BjyAuthorize\Provider\Identity\ZfcUserZendDb',
        'role_providers' => array(
            'BjyAuthorize\Provider\Role\Config' => array(
                'guest' => array('default' => true),
                'user'  => array('children' => array(
                    'admin' => array(),
                )),
            ),
        ),
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'pants' => array(),
            ),
        ),
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('guest', 'user'), 'pants', 'wear')
                ),
                'deny' => array(
                    // ...
                ),
            ),
        ),
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'index', 'action' => 'index', 'roles' => array('guest','user')),
                array('controller' => 'index', 'action' => 'stuff', 'roles' => array('user')),
                array('controller' => 'zfcuser', 'roles' => array())
            ),
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
