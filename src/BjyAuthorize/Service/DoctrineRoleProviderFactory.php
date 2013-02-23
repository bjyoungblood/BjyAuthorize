<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Exception\InvalidArgumentException;
use BjyAuthorize\Provider\Role\Doctrine;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of instantiating new instances
 * of {@see \BjyAuthorize\Provider\Role\DoctrineEntity}
 *
 * @author Tom Oram <tom@scl.co.uk>
 * @author Jérémy Huet <jeremy.huet@gmail.com>
 */
class DoctrineRoleProviderFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return \BjyAuthorize\Provider\Role\Doctrine
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (! isset($config['bjyauthorize']['role_providers']['BjyAuthorize\Provider\Role\DoctrineEntity']['role_entity_class'])) {
            throw new InvalidArgumentException('role_entity_class not set in the bjyauthorize role_providers config.');
        }
        $roleClass = $config['bjyauthorize']['role_providers']['BjyAuthorize\Provider\Role\DoctrineEntity']['role_entity_class'];

        if (! isset($config['bjyauthorize']['role_providers']['BjyAuthorize\Provider\Role\Doctrine']['object_manager'])) {
            throw new InvalidArgumentException('object_manager not set in the bjyauthorize role_providers config.');
        }
        $objectManagerLocatorKey = $config['bjyauthorize']['role_providers']['BjyAuthorize\Provider\Role\Doctrine']['object_manager'];

        $objectManager = $serviceLocator->get($objectManagerLocatorKey);
        $objectRepository = $objectManager->getRepository($roleClass);

        return new Doctrine($objectRepository);
    }
}
