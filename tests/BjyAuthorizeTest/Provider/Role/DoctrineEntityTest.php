<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\View;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Provider\Role\DoctrineEntity;

/**
 * {@see \BjyAuthorize\Provider\Role\DoctrineEntity} test
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class DoctrineEntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \BjyAuthorize\Provider\Role\DoctrineEntity
     */
    private $provider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    protected function setUp()
    {
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->provider = new DoctrineEntity($this->repository);
    }

    private function createRoleMock($name, $parent)
    {
        $role = $this->getMock('BjyAuthorize\Acl\HierarchicalRoleInterface');
        $role->expects($this->atLeastOnce())
            ->method('getRoleId')
            ->will($this->returnValue($name));

        $role->expects($this->atLeastOnce())
            ->method('getParent')
            ->will($this->returnValue($parent));

        return $role;

    }

    /**
     * @covers \BjyAuthorize\Provider\Role\DoctrineEntity::getRoles
     */
    public function testGetRolesWithNoParents()
    {
        // Set up mocks
        $roles = array(
            $this->createRoleMock('role1', null),
            $this->createRoleMock('role2', null)
        );

        $this->repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($roles));

        // Set up the expected outcome
        $expects = array(
            new \BjyAuthorize\Acl\Role('role1', null),
            new \BjyAuthorize\Acl\Role('role2', null),
        );

       $this->assertEquals($expects, $this->provider->getRoles());
    }

    /**
     * @covers \BjyAuthorize\Provider\Role\DoctrineEntity::getRoles
     */
    public function testGetRolesWithParents()
    {
        // Setup mocks
        $role1 = $this->createRoleMock('role1', null);
        $roles = array(
            $role1,
            $this->createRoleMock('role2', null),
            $this->createRoleMock('role3', $role1)
        );

        $this->repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($roles));

        // Set up the expected outcome
        $expectedRole1 = new \BjyAuthorize\Acl\Role('role1', null);
        $expects = array(
            $expectedRole1,
            new \BjyAuthorize\Acl\Role('role2', null),
            new \BjyAuthorize\Acl\Role('role3', $expectedRole1),
        );

       $this->assertEquals($expects, $this->provider->getRoles());
    }
}
