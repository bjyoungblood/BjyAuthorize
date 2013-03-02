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
use Zend\ServiceManager\ServiceManager;
use BjyAuthorize\Service\AuthenticationIdentityProviderServiceFactory;

/**
 * Factory test
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
    public function testAuthenticationIdentityProviderServiceFactory()
    {
        $config = array(
            'bjyauthorize' => array(
                'default_role'       => 'test-guest',
                'authenticated_role' => 'test-user'
            ),
        );

        $user = $this->getMock('ZfcUser\\Service\\User', array('getAuthService'));
        $auth = $this->getMock('Zend\\Authentication\\AuthenticationService');
        $user->expects($this->once())->method('getAuthService')->will($this->returnValue($auth));

        $serviceManager = new ServiceManager();
        $serviceManager->setService("zfcuser_user_service", $user);
        $serviceManager->setService("Config", $config);

        $authenticationFactory = new AuthenticationIdentityProviderServiceFactory();
        $authentication = $authenticationFactory->createService($serviceManager);

        $this->assertEquals($authentication->getDefaultRole(), 'test-guest');
        $this->assertEquals($authentication->getAuthenticatedRole(), 'test-user');
    }

}
