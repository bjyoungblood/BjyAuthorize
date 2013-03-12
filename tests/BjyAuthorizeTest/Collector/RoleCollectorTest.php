<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Collector;

use BjyAuthorize\Collector\RoleCollector;
use PHPUnit_Framework_TestCase;

/**
 * Tests for {@see \BjyAuthorize\Collector\RoleCollector}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RoleCollectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \BjyAuthorize\Collector\RoleCollector
     */
    protected $collector;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\BjyAuthorize\Provider\Identity\ProviderInterface
     */
    protected $identityProvider;

    /**
     * {@inheritDoc}
     *
     * @covers \BjyAuthorize\Collector\RoleCollector::__construct
     */
    public function setUp()
    {
        $this->identityProvider = $this->getMock('BjyAuthorize\\Provider\\Identity\\ProviderInterface');
        $this->collector        = new RoleCollector($this->identityProvider);
    }

    /**
     * @covers \BjyAuthorize\Collector\RoleCollector::collect
     * @covers \BjyAuthorize\Collector\RoleCollector::serialize
     * @covers \BjyAuthorize\Collector\RoleCollector::unserialize
     * @covers \BjyAuthorize\Collector\RoleCollector::getCollectedRoles
     */
    public function testCollect()
    {
        $role1    = $this->getMock('Zend\\Permissions\\Acl\\Role\\RoleInterface');
        $mvcEvent = $this->getMock('Zend\\Mvc\\MvcEvent');

        $role1->expects($this->any())->method('getRoleId')->will($this->returnValue('role1'));

        $this
            ->identityProvider
            ->expects($this->any())
            ->method('getIdentityRoles')
            ->will(
                $this->returnValue(
                    array(
                         $role1,
                         'role2',
                         'key' => 'role3',
                    )
                )
            );

        $this->collector->collect($mvcEvent);

        $roles = $this->collector->getCollectedRoles();

        $this->assertCount(3, $roles);
        $this->assertContains('role1', $roles);
        $this->assertContains('role2', $roles);
        $this->assertContains('role3', $roles);

        /* @var $collector \BjyAuthorize\Collector\RoleCollector */
        $collector = unserialize(serialize($this->collector));

        $collector->collect($mvcEvent);

        $roles = $this->collector->getCollectedRoles();

        $this->assertCount(3, $roles);
        $this->assertContains('role1', $roles);
        $this->assertContains('role2', $roles);
        $this->assertContains('role3', $roles);
    }

    /**
     * @covers \BjyAuthorize\Collector\RoleCollector::collect
     * @covers \BjyAuthorize\Collector\RoleCollector::getCollectedRoles
     */
    public function testTraversableCollect()
    {
        $role1    = $this->getMock('Zend\\Permissions\\Acl\\Role\\RoleInterface');
        $mvcEvent = $this->getMock('Zend\\Mvc\\MvcEvent');

        $role1->expects($this->any())->method('getRoleId')->will($this->returnValue('role1'));

        $this
            ->identityProvider
            ->expects($this->any())
            ->method('getIdentityRoles')
            ->will(
                $this->returnValue(
                    new \ArrayIterator(array(
                        $role1,
                        'role2',
                        'key' => 'role3',
                    ))
                )
            );

        $this->collector->collect($mvcEvent);

        $roles = $this->collector->getCollectedRoles();

        $this->assertCount(3, $roles);
        $this->assertContains('role1', $roles);
        $this->assertContains('role2', $roles);
        $this->assertContains('role3', $roles);
    }

    /**
     * @covers \BjyAuthorize\Collector\RoleCollector::getName
     */
    public function testGetName()
    {
        $this->assertInternalType('string', $this->collector->getName());
    }

    /**
     * @covers \BjyAuthorize\Collector\RoleCollector::getPriority
     */
    public function testGetPriority()
    {
        $this->assertInternalType('integer', $this->collector->getPriority());
    }
}
