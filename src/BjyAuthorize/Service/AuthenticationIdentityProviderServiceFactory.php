<?php

/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Simple authentication provider factory
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class AuthenticationIdentityProviderServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $user = $container->get('zfcuser_user_service');
        $simpleIdentityProvider = new AuthenticationIdentityProvider($user->getAuthService());
        $config = $container->get('BjyAuthorize\Config');

        $simpleIdentityProvider->setDefaultRole($config['default_role']);
        $simpleIdentityProvider->setAuthenticatedRole($config['authenticated_role']);

        return $simpleIdentityProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, AuthenticationIdentityProvider::class);
    }
}
