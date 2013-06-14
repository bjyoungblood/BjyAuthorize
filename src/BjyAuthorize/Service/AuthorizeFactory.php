<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use Zend\Cache\StorageFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Console\Request as ConsoleRequest;

/**
 * Factory responsible of building the {@see \BjyAuthorize\Service\Authorize} service
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class AuthorizeFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Service\Authorize
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config     = $serviceLocator->get('BjyAuthorize\Config');
        $authorize  = new Authorize($config, $serviceLocator);
        $application = $serviceLocator->get('Application');

        // Is caching enabled?
        if (isset($config['cache_enabled'])
            && $config['cache_enabled']
            && ! ($application->getRequest() instanceof ConsoleRequest)
        ) {
            // Check if cache options are set
            if (!isset($config['cache_options'])) {
                throw new \Exception('Cache is enabled but no cache_options are set.');
            }

            $authorize->setCacheEnabled();
            $authorize->setCacheKey($config['cache_key']);
            $authorize->setCache(StorageFactory::factory($config['cache_options']));
        }

        return $authorize;
    }
}
