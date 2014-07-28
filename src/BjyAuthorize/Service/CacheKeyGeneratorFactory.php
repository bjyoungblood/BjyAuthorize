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
 * Factory for building a cache key generator
 *
 * @author Steve Rhoades <sedonami@gmail.com>
 */
class CacheKeyGeneratorFactory implements FactoryInterface
{
    /**
     * Create a cache key
     *
     * @param   ServiceLocatorInterface $serviceLocator
     * @return  Callable
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('BjyAuthorize\Config');
        $cacheKey = (!empty($config['cache_key'])) ? $config['cache_key'] : 'bjyauthorize_acl';

        $callback = function () use ($cacheKey) {
            return $cacheKey;
        };

        return $callback;
    }
}
