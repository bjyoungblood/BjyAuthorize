<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 * @package        Zend_Service
 */

namespace BjyAuthorizeTest\Service;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Service\AuthenticationIdentityProviderServiceFactory;

/**
 * Factory test for {@see \BjyAuthorize\Service\AuthenticationIdentityProviderServiceFactory}
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class AuthenticationIdentityProviderServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers BjyAuthorize\Service\AuthenticationIdentityProviderServiceFactory::createService
     * @covers BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getDefaultRole
     * @covers BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getAuthenticatedRole
     */
    public function testCreateService()
    {
        $config = array(
            'default_role'       => 'test-guest',
            'authenticated_role' => 'test-user',
        );

        $user           = $this->getMock('ZfcUser\\Service\\User', array('getAuthService'));
        $auth           = $this->getMock('Zend\\Authentication\\AuthenticationService');
        $serviceLocator = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');

        $user->expects($this->once())->method('getAuthService')->will($this->returnValue($auth));
        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($service) use ($user, $config) {
                if ('zfcuser_user_service' === $service) {
                    return $user;
                }

                if ('BjyAuthorize\Config' === $service) {
                    return $config;
                }

                throw new \InvalidArgumentException();
            }));

        $authenticationFactory = new AuthenticationIdentityProviderServiceFactory();
        $authentication        = $authenticationFactory->createService($serviceLocator);

        $this->assertEquals($authentication->getDefaultRole(), 'test-guest');
        $this->assertEquals($authentication->getAuthenticatedRole(), 'test-user');
    }

}
