<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Guard\Route;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Guard\Route}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RouteGuardServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Guard\Route
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('BjyAuthorize\Config');

        return new Route($config['guards']['BjyAuthorize\Guard\Route'], $serviceLocator);
    }
}
