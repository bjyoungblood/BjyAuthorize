<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of building a set of {@see \BjyAuthorize\Guard\GuardInterface}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class GuardsServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Guard\GuardInterface[]
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('BjyAuthorize\Config');
        $guards = array();

        foreach ($config['guards'] as $guardName => $guardConfig) {
            if ($serviceLocator->has($guardName)) {
                $guards[] = $serviceLocator->get($guardName);
            } else {
                $guards[] = new $guardName($guardConfig, $serviceLocator);
            }

        }

        return $guards;
    }
}
