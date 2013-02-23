<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize;

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
        'BjyAuthorize\Service\Authorize' => 'BjyAuthorize\Service\AuthorizeFactory',

        'BjyAuthorize\Provider\Identity\ZfcUserZendDb' => function (ServiceLocatorInterface $serviceLocator) {
            /* @var $adapter \Zend\Db\Adapter\Adapter */
            $adapter     = $serviceLocator->get('zfcuser_zend_db_adapter');
            /* @var $userService \ZfcUser\Service\User */
            $userService = $serviceLocator->get('zfcuser_user_service');

            return new Provider\Identity\ZfcUserZendDb($adapter, $userService);
        },

        'BjyAuthorize\View\UnauthorizedStrategy' => function (ServiceLocatorInterface $serviceLocator) {
            $config = $serviceLocator->get('Config');

            return new View\UnauthorizedStrategy($config['bjyauthorize']['template']);
        },

        'BjyAuthorize\Provider\Role\ZendDb' => function (ServiceLocatorInterface $serviceLocator) {
            $config = $serviceLocator->get('Config');

            foreach ($config['bjyauthorize']['role_providers'] as $class => $options) {
                return new $class($options, $serviceLocator);
            }
        },

        'BjyAuthorize\Provider\Role\Doctrine' => 'BjyAuthorize\Service\DoctrineRoleProviderFactory',
        
        'BjyAuthorize\Guard\Controller' => function (\Zend\ServiceManager\ServiceLocatorInterface $sl) {
            $config = $sl->get('config');
            
            return new \BjyAuthorize\Guard\Controller($config['bjyauthorize']['guards']['BjyAuthorize\Guard\Controller'], $sl);
        },
        
        'BjyAuthorize\Guard\Route'      => function (\Zend\ServiceManager\ServiceLocatorInterface $sl) {
            $config = $sl->get('config');
            
            return new \BjyAuthorize\Guard\Route($config['bjyauthorize']['guards']['BjyAuthorize\Guard\Route'], $sl);
        },

        'BjyAuthorize\Collector\RoleCollector' => function (ServiceLocatorInterface $serviceLocator) {
            $config = $serviceLocator->get('Config');

            return new \BjyAuthorize\Collector\RoleCollector($serviceLocator->get($config['bjyauthorize']['identity_provider']));
        }
    ),
);
