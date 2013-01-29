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
    const DEFAULT_ROLE_ENTITY_CLASS = 'BjyAuthorize\Entity\Role';

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

        $roleEntityClass = self::DEFAULT_ROLE_ENTITY_CLASS;

        if (isset($config['bjy_authorize']['role_providers']['BjyAuthorize\Provider\Role\DoctrineEntity']['role_entity_class'])) {
            $roleEntityClass = $config['bjy_authorize']['role_providers']['BjyAuthorize\Provider\Role\DoctrineEntity']['role_entity_class'];
        }

        /* @var $objectManager \Doctrine\ORM\EntityManager */
        $objectManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        /* @var Doctrine\Common\Persistence\ObjectRepository $objectRository */
        $objectRepository = $objectManager->getRepository($roleEntityClass);

        return new DoctrineEntity($objectRepository);
    }
}
