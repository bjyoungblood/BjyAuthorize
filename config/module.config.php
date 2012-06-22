<?php

return array(
    'bjyauthorize' => array(
        'default_role' => 'guest',
        'identityProvider' => 'BjyAuthorize\Provider\Identity\ZfcUserZendDb',
        'roleProviders' => array(
            'BjyAuthorize\Provider\Role\Config' => array(
                'guest' => array('default' => true),
                'user'  => array('children' => array(
                    'admin' => array(),
                )),
            ),
        ),
        'resourceProviders' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'pants' => array(),
            ),
        ),
        'ruleProviders' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('guest', 'user'), 'pants', 'wear')
                ),
                'deny' => array(
                    // ...
                ),
            ),
        ),
    ),
);
