<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
*
* @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
* @license http://framework.zend.com/license/new-bsd New BSD License
*/

namespace BjyAuthorize\Service;

use BjyAuthorize\Provider\Identity\AuthenticationDoctrine;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory responsible of instantiating new instances
 * of {@see \BjyAuthorize\Provider\Identity\AuthenticationDoctrine}
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class AuthenticationDoctrineFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \BjyAuthorize\Provider\Identity\AuthenticationDoctrine
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $authService \Zend\Authentication\AuthenticationService */
        $authService = $serviceLocator->get('zfcuser_auth_service');
        $identityProvider = new AuthenticationDoctrine($authService);

        $config = $serviceLocator->get('Config');
        $identityProvider->setDefaultRole($config['bjyauthorize']['default_role']);

        return $identityProvider;
    }
}
