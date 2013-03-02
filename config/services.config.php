<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize;

use BjyAuthorize\Provider\Role\ZendDb;
use Zend\ServiceManager\ServiceLocatorInterface;

return array(
    'initializers' => array(
        function ($instance, ServiceLocatorInterface $serviceLocator) {
            if ($instance instanceof Service\AuthorizeAwareInterface) {
                /* @var $authorize \BjyAuthorize\Service\Authorize */
                $authorize = $serviceLocator->get('BjyAuthorize\Service\Authorize');

                $instance->setAuthorizeService($authorize);
            }
        }
    ),
    'factories' => array(
        'BjyAuthorize\Provider\Identity\ZfcUserZendDb' => function (ServiceLocatorInterface $serviceLocator) {
            /* @var $adapter \Zend\Db\Adapter\Adapter */
            $adapter     = $serviceLocator->get('zfcuser_zend_db_adapter');
            /* @var $userService \ZfcUser\Service\User */
            $userService = $serviceLocator->get('zfcuser_user_service');
            $config      = $serviceLocator->get('Config');

            $provider = new Provider\Identity\ZfcUserZendDb($adapter, $userService);

            $provider->setDefaultRole($config['bjyauthorize']['default_role']);

            return $provider;
        },

        'BjyAuthorize\View\UnauthorizedStrategy' => function (ServiceLocatorInterface $serviceLocator) {
            $config = $serviceLocator->get('Config');

            return new View\UnauthorizedStrategy($config['bjyauthorize']['template']);
        },

        'BjyAuthorize\Provider\Role\ZendDb' => function (ServiceLocatorInterface $serviceLocator) {
            $config = $serviceLocator->get('Config');

            return new ZendDb(
                $config['bjyauthorize']['role_providers']['BjyAuthorize\Provider\Role\ZendDb'],
                $serviceLocator
            );
        },

        'BjyAuthorize\Guard\Controller' => function (ServiceLocatorInterface $sl) {
            $config = $sl->get('config');

            return new Guard\Controller($config['bjyauthorize']['guards']['BjyAuthorize\Guard\Controller'], $sl);
        },

        'BjyAuthorize\Guard\Route'      => function (ServiceLocatorInterface $sl) {
            $config = $sl->get('config');

            return new Guard\Route($config['bjyauthorize']['guards']['BjyAuthorize\Guard\Route'], $sl);
        },

        'BjyAuthorize\Collector\RoleCollector' => function (ServiceLocatorInterface $serviceLocator) {
            $config = $serviceLocator->get('Config');

            /* @var $identityProvider \BjyAuthorize\Provider\Identity\ProviderInterface */
            $identityProvider = $serviceLocator->get($config['bjyauthorize']['identity_provider']);

            return new Collector\RoleCollector($identityProvider);
        }
    ),
);
