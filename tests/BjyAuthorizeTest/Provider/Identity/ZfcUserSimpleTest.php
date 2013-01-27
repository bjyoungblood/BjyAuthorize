<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Provider\Identity;

use PHPUnit_Framework_TestCase;

/**
 * {@see \BjyAuthorize\Provider\Identity\ZfcUserSimple} test
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class ZfcUserSimpleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers BjyAuthorize\Service\ZfcUserSimpleServiceFactory::createService
     * @covers BjyAuthorize\Provider\Identity\ZfcUserSimple::getDefaultRole
     * @covers BjyAuthorize\Provider\Identity\ZfcUserSimple::getAuthenticatedRole
     */
    public function testZfcUserSimpleFactory()
    {
        $config = array(
            'bjyauthorize' => array(
                'default_role'          => 'guest',
                'authenticated_role'       => 'user'
            ),
        );

        $services = include __DIR__ . '/../../../../config/module.config.php';
        $serviceManagerConfig = new \Zend\Mvc\Service\ServiceManagerConfig($services['service_manager']);
        $serviceManager = new \Zend\ServiceManager\ServiceManager($serviceManagerConfig);

        $user = $this->getMock('ZfcUser\Service\User', array('getAuthService'));
        $auth = $this->getMock('Zend\Authentication\AuthenticationService');
        $user->expects($this->once())->method('getAuthService')->will($this->returnValue($auth));
        $serviceManager->setService("zfcuser_user_service", $user);
        $serviceManager->setService("Config", $config);
        $simpleIdentitiyProvider = $serviceManager->get('BjyAuthorize\Provider\Identity\ZfcUserSimple');

        $this->assertEquals($simpleIdentitiyProvider->getDefaultRole(), 'guest');
        $this->assertEquals($simpleIdentitiyProvider->getAuthenticatedRole(), 'user');
    }

    /**
     * @covers BjyAuthorize\Provider\Identity\ZfcUserSimple::getIdentityRoles
     */
    public function testZfcUserSimpleIfAuthenticated()
    {
        $authentication = $this->getMock('Zend\Authentication\AuthenticationService', array('getIdentity'));
        $authentication->expects($this->once())->method('getIdentity')->will($this->returnValue('foo'));

        $simpleIdentityProvider = new \BjyAuthorize\Provider\Identity\ZfcUserSimple($authentication);
        $simpleIdentityProvider->setDefaultRole('guest');
        $simpleIdentityProvider->setAuthenticatedRole('user');
        $roles = $simpleIdentityProvider->getIdentityRoles();

        $this->assertEquals($roles, array('user'));
    }

    /**
     * @covers BjyAuthorize\Provider\Identity\ZfcUserSimple::getIdentityRoles
     */
    public function testZfcUserSimpleIfUnauthenticated()
    {
        $authentication = $this->getMock('Zend\Authentication\AuthenticationService', array('getIdentity'));
        $authentication->expects($this->once())->method('getIdentity')->will($this->returnValue(null));

        $simpleIdentityProvider = new \BjyAuthorize\Provider\Identity\ZfcUserSimple($authentication);
        $simpleIdentityProvider->setDefaultRole('guest');
        $simpleIdentityProvider->setAuthenticatedRole('user');
        $roles = $simpleIdentityProvider->getIdentityRoles();

        $this->assertEquals($roles, array('guest'));
    }

    /**
     * @covers BjyAuthorize\Provider\Identity\ZfcUserSimple::getIdentityRoles
     */
    public function testZfcUserSimpleIfAuthenticatedWithRoleInterface()
    {
        $authentication = $this->getMock('Zend\Authentication\AuthenticationService', array('getIdentity'));
        $authentication->expects($this->once())->method('getIdentity')->will($this->returnValue('foo'));

        $simpleIdentityProvider = new \BjyAuthorize\Provider\Identity\ZfcUserSimple($authentication);
        $authorizedRole = $this->getMock('Zend\Permissions\Acl\Role\RoleInterface', array('getRoleId'));
        $authorizedRole->expects($this->once())->method('getRoleId')->will($this->returnValue('user'));

        $simpleIdentityProvider->setAuthenticatedRole($authorizedRole);
        $roles = $simpleIdentityProvider->getIdentityRoles();

        $this->assertEquals($roles, array('user'));
    }

    /**
     * @covers BjyAuthorize\Provider\Identity\ZfcUserSimple::getIdentityRoles
     */
    public function testZfcUserSimpleIfUnauthenticatedWithRoleInterface()
    {
        $authentication = $this->getMock('Zend\Authentication\AuthenticationService', array('getIdentity'));
        $authentication->expects($this->once())->method('getIdentity')->will($this->returnValue(null));

        $simpleIdentityProvider = new \BjyAuthorize\Provider\Identity\ZfcUserSimple($authentication);
        $defaultRole = $this->getMock('Zend\Permissions\Acl\Role\RoleInterface', array('getRoleId'));
        $defaultRole->expects($this->once())->method('getRoleId')->will($this->returnValue('guest'));

        $simpleIdentityProvider->setDefaultRole($defaultRole);
        $roles = $simpleIdentityProvider->getIdentityRoles();

        $this->assertEquals($roles, array('guest'));
    }
}
