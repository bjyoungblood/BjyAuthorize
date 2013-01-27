<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\View;

use PHPUnit_Framework_TestCase;

/**
 * {@see \BjyAuthorize\Provider\Identity\ZfcUserSimple} test
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class ZfcUserSimpleTest extends PHPUnit_Framework_TestCase
{
    public function testZfcUserSimpleFactory()
    {
        $config = array(
            'bjyauthorize' => array(
                'default_role'          => 'guest',
                'authorized_role'       => 'user'
            ),
        );

        $services = include __DIR__ . '/../../../../config/services.config.php';
        $serviceManagerConfig = new \Zend\Mvc\Service\ServiceManagerConfig($services);
        $serviceManager = new \Zend\ServiceManager\ServiceManager($serviceManagerConfig);
        $user = $this->getMock('ZfcUser\Service\User');
        $serviceManager->setService("zfcuser_user_service", $user);
        $serviceManager->setService("Config", $config);
        $simpleIdentitiyProvider = $serviceManager->get('BjyAuthorize\Provider\Identity\ZfcUserSimple');

        $this->assertEquals($simpleIdentitiyProvider->getDefaultRole(), 'guest');
        $this->assertEquals($simpleIdentitiyProvider->getDefaultAuthorizedRole(), 'user');
    }

    public function testZfcUserSimpleIfAuthenticated()
    {
        $user = $this->getMock('ZfcUser\Service\User', array('getAuthService'));
        $authentication = $this->getMock('Zend\Authentication\AuthenticationService', array('getIdentity'));
        $authentication->expects($this->once())->method('getIdentity')->will($this->returnValue('foo'));
        $user->expects($this->once())->method('getAuthService')->will($this->returnValue($authentication));
        $simpleIdentityProvider = new \BjyAuthorize\Provider\Identity\ZfcUserSimple($user);
        $simpleIdentityProvider->setDefaultRole('guest');
        $simpleIdentityProvider->setDefaultAuthorizedRole('user');
        $roles = $simpleIdentityProvider->getIdentityRoles();
        $this->assertEquals($roles, array('user'));
    }

    public function testZfcUserSimpleIfUnauthenticated()
    {
        $user = $this->getMock('ZfcUser\Service\User', array('getAuthService'));
        $authentication = $this->getMock('Zend\Authentication\AuthenticationService', array('getIdentity'));
        $authentication->expects($this->once())->method('getIdentity')->will($this->returnValue(null));
        $user->expects($this->once())->method('getAuthService')->will($this->returnValue($authentication));
        $simpleIdentityProvider = new \BjyAuthorize\Provider\Identity\ZfcUserSimple($user);
        $simpleIdentityProvider->setDefaultRole('guest');
        $simpleIdentityProvider->setDefaultAuthorizedRole('user');
        $roles = $simpleIdentityProvider->getIdentityRoles();
        $this->assertEquals($roles, array('guest'));
    }
}
