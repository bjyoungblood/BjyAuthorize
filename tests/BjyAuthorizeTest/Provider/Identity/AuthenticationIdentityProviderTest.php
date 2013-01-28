<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Provider\Identity;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider;

/**
 * {@see \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider} test
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class AuthenticationIdentityProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getIdentityRoles
     */
    public function testAuthenticationIdentityProviderIfAuthenticated()
    {
        $authentication = $this->getMock('Zend\Authentication\AuthenticationService', array('getIdentity'));
        $authentication->expects($this->once())->method('getIdentity')->will($this->returnValue('foo'));

        $simpleIdentityProvider = new AuthenticationIdentityProvider($authentication);
        $simpleIdentityProvider->setDefaultRole('guest');
        $simpleIdentityProvider->setAuthenticatedRole('user');
        $roles = $simpleIdentityProvider->getIdentityRoles();

        $this->assertEquals($roles, array('user'));
    }

    /**
     * @covers BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getIdentityRoles
     */
    public function testAuthenticationIdentityProviderIfUnauthenticated()
    {
        $authentication = $this->getMock('Zend\Authentication\AuthenticationService', array('getIdentity'));
        $authentication->expects($this->once())->method('getIdentity')->will($this->returnValue(null));

        $simpleIdentityProvider = new AuthenticationIdentityProvider($authentication);
        $simpleIdentityProvider->setDefaultRole('guest');
        $simpleIdentityProvider->setAuthenticatedRole('user');
        $roles = $simpleIdentityProvider->getIdentityRoles();

        $this->assertEquals($roles, array('guest'));
    }

    /**
     * @covers BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getIdentityRoles
     */
    public function testAuthenticationIdentityProviderIfAuthenticatedWithRoleInterface()
    {
        $authentication = $this->getMock('Zend\Authentication\AuthenticationService', array('getIdentity'));
        $authentication->expects($this->once())->method('getIdentity')->will($this->returnValue('foo'));

        $simpleIdentityProvider = new AuthenticationIdentityProvider($authentication);
        $authorizedRole = $this->getMock('Zend\Permissions\Acl\Role\RoleInterface', array('getRoleId'));
        $authorizedRole->expects($this->once())->method('getRoleId')->will($this->returnValue('user'));

        $simpleIdentityProvider->setAuthenticatedRole($authorizedRole);
        $roles = $simpleIdentityProvider->getIdentityRoles();

        $this->assertEquals($roles, array('user'));
    }

    /**
     * @covers BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getIdentityRoles
     */
    public function testAuthenticationIdentityProviderIfUnauthenticatedWithRoleInterface()
    {
        $authentication = $this->getMock('Zend\Authentication\AuthenticationService', array('getIdentity'));
        $authentication->expects($this->once())->method('getIdentity')->will($this->returnValue(null));

        $simpleIdentityProvider = new AuthenticationIdentityProvider($authentication);
        $defaultRole = $this->getMock('Zend\Permissions\Acl\Role\RoleInterface', array('getRoleId'));
        $defaultRole->expects($this->once())->method('getRoleId')->will($this->returnValue('guest'));

        $simpleIdentityProvider->setDefaultRole($defaultRole);
        $roles = $simpleIdentityProvider->getIdentityRoles();

        $this->assertEquals($roles, array('guest'));
    }
}
