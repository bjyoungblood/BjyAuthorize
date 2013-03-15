<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Exception\InvalidArgumentException;
use BjyAuthorize\Provider\Role\ObjectRepositoryProvider;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Provider\Role\ObjectRepositoryProvider}
 *
 * @author Tom Oram <tom@scl.co.uk>
 * @author Jérémy Huet <jeremy.huet@gmail.com>
 */
class ObjectRepositoryRoleProviderFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Provider\Role\ObjectRepositoryProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('BjyAuthorize\Config');

        if ( ! isset($config['role_providers']['BjyAuthorize\Provider\Role\ObjectRepositoryProvider'])) {
            throw new InvalidArgumentException(
                'Config for "BjyAuthorize\Provider\Role\ObjectRepositoryProvider" not set'
            );
        }

        $providerConfig = $config['role_providers']['BjyAuthorize\Provider\Role\ObjectRepositoryProvider'];

        if ( ! isset($providerConfig['role_entity_class'])) {
            throw new InvalidArgumentException('role_entity_class not set in the bjyauthorize role_providers config.');
        }

        if ( ! isset($providerConfig['object_manager'])) {
            throw new InvalidArgumentException('object_manager not set in the bjyauthorize role_providers config.');
        }

        /* @var $objectManager \Doctrine\Common\Persistence\ObjectManager */
        $objectManager = $serviceLocator->get($providerConfig['object_manager']);

        return new ObjectRepositoryProvider($objectManager->getRepository($providerConfig['role_entity_class']));
    }
}
