<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\View;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Provider\Identity\ZfcUserDoctrineEntity;

/**
 * {@see \BjyAuthorize\Provider\Identity\ZfcUserDoctrineEntity} test
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ZfcUserDoctrineEntityTest extends PHPUnit_Framework_TestCase
{
    const DEFAULT_ROLE = 'the_default_role';

    private $provider;
    private $authService;

    protected function setUp()
    {
        $this->authService = $this->getMock('Zend\Authentication\AuthenticationService');

        $this->provider = new ZfcUserDoctrineEntity($this->authService);
        $this->provider->setDefaultRole(self::DEFAULT_ROLE);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\ZfcUserDoctrineEntity::getDefaultRole
     */
    public function testGetDefaultRole()
    {
        $this->assertEquals(self::DEFAULT_ROLE, $this->provider->getDefaultRole());
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\ZfcUserDoctrineEntity::getIdentityRoles
     */
    public function testGetDefaultRolesWithNoIdentity()
    {
        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(false));

        $this->authService->expects($this->never())
            ->method('getIdentity');

        $roles = $this->provider->getIdentityRoles();

        $this->assertEquals(self::DEFAULT_ROLE, $roles);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\ZfcUserDoctrineEntity::getIdentityRoles
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

        $this->assertEquals(self::DEFAULT_ROLE, $roles);
    }

    
    /**
     * @covers \BjyAuthorize\Provider\Identity\ZfcUserDoctrineEntity::getIdentityRoles
     */
    public function testGetDefaultRoles()
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
