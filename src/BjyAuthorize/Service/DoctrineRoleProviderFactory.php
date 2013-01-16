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
use BjyAuthorize\Provider\Role\Doctrine;

/**
 * Factory responsible of instantiating new instances
 * of {@see \BjyAuthorize\Provider\Role\Doctrine}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class DoctrineRoleProviderFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Provider\Role\Doctrine
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $objectManager \Doctrine\ORM\EntityManager */
        $objectManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

        return new Doctrine(array(), $objectManager);
    }
}
