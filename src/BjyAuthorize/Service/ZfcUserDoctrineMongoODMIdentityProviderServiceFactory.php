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
use BjyAuthorize\Provider\Identity\ZfcUserDoctrineMongoODM;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Provider\Identity\ZfcUserDoctrineMongoODM}
 *
 * @author Mat Wright <mat@bstechnologies.com>
 */
class ZfcUserDoctrineMongoODMIdentityProviderServiceFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Provider\Identity\ZfcUserDoctrineMongoODM
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $userService = $serviceLocator->get('zfcuser_user_service');
        $config = $serviceLocator->get('BjyAuthorize\Config');
        
        $provider = new ZfcUserDoctrineMongoODM($userService);
        
        $provider->setDefaultRole($config['default_role']);
        
        return $provider;
    }
}
