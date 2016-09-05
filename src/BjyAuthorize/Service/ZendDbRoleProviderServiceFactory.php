<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Provider\Role\ZendDb;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Provider\Role\ZendDb}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ZendDbRoleProviderServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ZendDb(
            $container->get('BjyAuthorize\Config')['role_providers']['BjyAuthorize\Provider\Role\ZendDb'],
            $container
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Provider\Role\ZendDb
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ZendDb::class);
    }
}
