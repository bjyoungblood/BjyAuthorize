<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Collector\RoleCollector;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Collector\RoleCollector}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RoleCollectorServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Collector\RoleCollector
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $identityProvider \BjyAuthorize\Provider\Identity\ProviderInterface */
        $identityProvider = $serviceLocator->get('BjyAuthorize\Provider\Identity\ProviderInterface');

        return new RoleCollector($identityProvider);
    }
}
