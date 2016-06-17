<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\StorageFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for building the cache storage
 *
 * @author Christian Bergau <cbergau86@gmail.com>
 */
class CacheFactory implements FactoryInterface
{
    /**
     * Create a cache
     *
     * @param   ServiceLocatorInterface $serviceLocator
     * @return  StorageInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $adapterName = null;
        $options = $serviceLocator->get('BjyAuthorize\Config');
        if (is_array($options)
            && isset($options['cache_options'])
            && isset($options['cache_options']['adapter'])
            && isset($options['cache_options']['adapter']['name'])
        ) {
            $adapterName = $options['cache_options']['adapter']['name'];
        }

        // Create adapter via serviceLocator if possible
        if (!empty($adapterName) && $serviceLocator->has($adapterName)) {
            return $serviceLocator->get($adapterName);
        } else {
            return StorageFactory::factory($options['cache_options']);
        }
    }
}
