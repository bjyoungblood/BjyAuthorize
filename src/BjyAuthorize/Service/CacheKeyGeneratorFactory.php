<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use Interop\Container\ContainerInterface;
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
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return \Closure
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('BjyAuthorize\Config');
        $cacheKey = empty($config['cache_key']) ? 'bjyauthorize_acl' : (string)$config['cache_key'];

        return function () use ($cacheKey) {
            return $cacheKey;
        };
    }

    /**
     * Create a cache key
     *
     * @param   ServiceLocatorInterface $serviceLocator
     * @return  callable
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, '');
    }
}
