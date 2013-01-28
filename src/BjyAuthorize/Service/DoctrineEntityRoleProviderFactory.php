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
     * @return \BjyAuthorize\Provider\Role\DoctrineEntity
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $objectManager \Doctrine\ORM\EntityManager */
        $objectManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

        /* @var $appConfig array */
        $appConfig = $serviceLocator->get('Configuration');

        $config = array();
        if (isset($appConfig['bjy_authorize']['role_providers']['BjyAuthorize\Provider\Role\DoctrineEntity'])) {
            $config = $appConfig['bjy_authorize']['role_providers']['BjyAuthorize\Provider\Role\DoctrineEntity'];
        }

        return new DoctrineEntity($config, $objectManager);
    }
}
