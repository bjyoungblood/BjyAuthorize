<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\View;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity;

/**
 * {@see \BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity} test
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class AuthenticationDoctrineEntityTest extends PHPUnit_Framework_TestCase
{
    const DEFAULT_ROLE = 'the_default_role';

    /**
     * @var \BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity
     */
    private $provider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $authService;

    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity::__construct
     */
    protected function setUp()
    {
        $this->authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $this->provider    = new AuthenticationDoctrineEntity($this->authService);

        $this->provider->setDefaultRole(self::DEFAULT_ROLE);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity::getDefaultRole
     */
    public function testGetDefaultRole()
    {
        $this->assertEquals(self::DEFAULT_ROLE, $this->provider->getDefaultRole());
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity::getIdentityRoles
     */
    public function testGetDefaultRolesWithNoIdentity()
    {
        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(false));

        $this->authService->expects($this->never())
            ->method('getIdentity');

        $roles = $this->provider->getIdentityRoles();

        $this->assertEquals(array(self::DEFAULT_ROLE), $roles);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity::getIdentityRoles
     */
    public function testGetDefaultRolesWithBadIdentityType()
    {
        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $this->authService->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue(new \StdClass));

        $roles = $this->provider->getIdentityRoles();

        $this->assertEquals(array(self::DEFAULT_ROLE), $roles);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity::getIdentityRoles
     */
    public function testGetDefaultRolesForRoleInterfaceUser()
    {
        // Create user and role mocks
        $user = $this->getMock('Zend\Permissions\Acl\Role\RoleInterface');
        $user->expects($this->once())
            ->method('getRoleId')
            ->will($this->returnValue('the_role'));

        // Prepare the authService Mock
        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $this->authService->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        // Test
        $roles = $this->provider->getIdentityRoles();

        $this->assertEquals(array('the_role'), $roles);
        
    }
    
    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity::getIdentityRoles
     */
    public function testGetDefaultRolesForRoleProviderUser()
    {
        // Create user and role mocks
        $role1 = $this->getMock('Zend\Permissions\Acl\Role\RoleInterface');
        $role1->expects($this->once())
            ->method('getRoleId')
            ->will($this->returnValue('role1'));

        $role2 = $this->getMock('Zend\Permissions\Acl\Role\RoleInterface');
        $role2->expects($this->once())
            ->method('getRoleId')
            ->will($this->returnValue('role2'));

        $user = $this->getMock('BjyAuthorize\Provider\Role\ProviderInterface');
        $user->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue(array($role1, $role2)));

        // Prepare the authService Mock
        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $this->authService->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        // Test
        $roles = $this->provider->getIdentityRoles();

        $this->assertEquals(array('role1', 'role2'), $roles);
        
    }
}
