<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Guard\Controller;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Guard\Controller}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ControllerGuardServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Controller(
            $container->get('BjyAuthorize\Config')['guards']['BjyAuthorize\Guard\Controller'],
            $container
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Guard\Controller
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Controller::class);
    }
}
