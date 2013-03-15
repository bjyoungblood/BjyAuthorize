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
 * Base factory responsible of instantiating providers
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
abstract class BaseProvidersServiceFactory implements FactoryInterface
{
    const PROVIDER_SETTING = 'providers';

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config    = $serviceLocator->get('BjyAuthorize\Config');
        $providers = array();

        foreach ($config[static::PROVIDER_SETTING] as $providerName => $providerConfig) {
            if ($serviceLocator->has($providerName)) {
                $providers[] = $serviceLocator->get($providerName);
            } else {
                $providers[] = new $providerName($providerConfig, $serviceLocator);
            }
        }

        return $providers;
    }
}
