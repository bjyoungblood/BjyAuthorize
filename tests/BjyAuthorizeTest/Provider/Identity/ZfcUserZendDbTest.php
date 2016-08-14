<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Provider\Identity;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Provider\Identity\ZfcUserZendDb;

/**
 * {@see \BjyAuthorize\Provider\Identity\ZfcUserZendDb} test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ZfcUserZendDbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Authentication\AuthenticationService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authService;

    /**
     * @var \ZfcUser\Service\User|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userService;

    /**
     * @var \Zend\Db\TableGateway\TableGateway|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tableGateway;

    /**
     * @var \BjyAuthorize\Provider\Identity\ZfcUserZendDb
     */
    protected $provider;

    /**
     * {@inheritDoc}
     *
     * @covers \BjyAuthorize\Provider\Identity\ZfcUserZendDb::__construct
     */
    public function setUp()
    {
        $this->authService  = $this->getMock('Zend\Authentication\AuthenticationService');
        $this->userService  = $this->getMock('ZfcUser\Service\User', array('getAuthService'));
        $this->tableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway', array(), array(), '', false);

        $this
            ->userService
            ->expects($this->any())
            ->method('getAuthService')
            ->will($this->returnValue($this->authService));

        $this->provider = new ZfcUserZendDb($this->tableGateway, $this->userService);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\ZfcUserZendDb::getIdentityRoles
     * @covers \BjyAuthorize\Provider\Identity\ZfcUserZendDb::setDefaultRole
     */
    public function testGetIdentityRolesWithNoAuthIdentity()
    {
        $this->provider->setDefaultRole('test-default');

        $this->assertSame(array('test-default'), $this->provider->getIdentityRoles());
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\ZfcUserZendDb::getIdentityRoles
     */
    public function testSetGetDefaultRole()
    {
        $this->provider->setDefaultRole('test');
        $this->assertSame('test', $this->provider->getDefaultRole());

        $role = $this->getMock('Zend\\Permissions\\Acl\\Role\\RoleInterface');
        $this->provider->setDefaultRole($role);
        $this->assertSame($role, $this->provider->getDefaultRole());

        $this->setExpectedException('BjyAuthorize\\Exception\\InvalidRoleException');
        $this->provider->setDefaultRole(false);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\ZfcUserZendDb::getIdentityRoles
     */
    public function testGetIdentityRoles()
    {
        $roles = $this->provider->getIdentityRoles();
        $this->assertEquals($roles, array(null));
    }
}
