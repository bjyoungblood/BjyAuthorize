<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Provider\Role\Config;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Provider\Role\Config}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigRoleProviderServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Config(
            $container->get('BjyAuthorize\Config')['role_providers']['BjyAuthorize\Provider\Role\Config']
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Provider\Role\Config
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Config::class);
    }
}
