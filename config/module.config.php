<?php

return array(
    'bjyauthorize' => array(
        'default_role' => 'guest',
        'identityProvider' => 'BjyAuthorize\Provider\Identity\ZfcUserZendDb',
        'roleProviders' => array(
            'BjyAuthorize\Provider\Role\Config' => array(
                'guest' => array('default' => 1),
                'user'  => array('default' => 0, 'children' => array(
                    'admin' => array(),
                )),
            ),
        ),
        'resourceProviders' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                // ...
            ),
        ),
        'ruleProviders' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    // ...
                ),
                'deny' => array(
                    // ...
                ),
            ),
        ),
    ),
);
