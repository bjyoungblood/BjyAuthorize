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
use BjyAuthorize\Provider\Role\DoctrineEntity;

/**
 * Factory responsible of instantiating new instances
 * of {@see \BjyAuthorize\Provider\Role\DoctrineEntity}
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class DoctrineEntityRoleProviderFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return \BjyAuthorize\Provider\Role\DoctrineEntity
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['bjyauthorize']['role_providers']['BjyAuthorize\Provider\Role\DoctrineEntity']['role_entity_class'])) {
            return null; // What's the appropriate response
        }

        $roleEntityClass = $config['bjyauthorize']['role_providers']['BjyAuthorize\Provider\Role\DoctrineEntity']['role_entity_class'];

        /* @var $objectManager \Doctrine\ORM\EntityManager */
        $objectManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        /* @var Doctrine\Common\Persistence\ObjectRepository $objectRository */
        $objectRepository = $objectManager->getRepository($roleEntityClass);

        return new DoctrineEntity($objectRepository);
    }
}
