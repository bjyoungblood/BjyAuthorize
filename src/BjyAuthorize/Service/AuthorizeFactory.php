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

        if (
            isset($config['cache_adapter'])
            && isset($config['cache_key'])
            && ! ($application->getRequest() instanceof ConsoleRequest)
        ) {
            $authorize->setCacheKey($config['cache_key']);
            $authorize->setCache(new $config['cache_adapter']);
        }

        return $authorize;
    }
}
