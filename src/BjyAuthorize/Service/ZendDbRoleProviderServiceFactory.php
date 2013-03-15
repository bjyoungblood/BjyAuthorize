<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Provider\Role\ZendDb;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Provider\Identity\ZfcUserZendDb}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ZendDbRoleProviderServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Provider\Role\ZendDb
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('BjyAuthorize\Config');

        return new ZendDb($config['role_providers']['BjyAuthorize\Provider\Role\ZendDb'], $serviceLocator);
    }
}
