<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Provider\Identity\ZfcUserZendDb;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Provider\Identity\ZfcUserZendDb}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ZfcUserZendDbIdentityProviderServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Provider\Identity\ZfcUserZendDb
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $adapter \Zend\Db\Adapter\Adapter */
        $adapter     = $serviceLocator->get('zfcuser_zend_db_adapter');
        /* @var $userService \ZfcUser\Service\User */
        $userService = $serviceLocator->get('zfcuser_user_service');
        $config      = $serviceLocator->get('BjyAuthorize\Config');

        $provider = new ZfcUserZendDb($adapter, $userService);

        $provider->setDefaultRole($config['default_role']);

        return $provider;
    }
}
