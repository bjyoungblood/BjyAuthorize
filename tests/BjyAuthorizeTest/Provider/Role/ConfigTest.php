<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Provider\Role;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Provider\Role\Config;

/**
 * Config resource provider test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Provider\Role\Config::__construct
     * @covers \BjyAuthorize\Provider\Role\Config::loadRole
     * @covers \BjyAuthorize\Provider\Role\Config::getRoles
     */
    public function testConstructor()
    {
        $config = new Config(
            array(
                'role1' => array(),
                'role2',
                'role3' => array(
                    'children' => array('role4'),
                ),
                'role5' => array(
                    'children' => array(
                        'role6',
                        'role7' => array(),
                    ),
                ),
            )
        );

        $roles = $config->getRoles();

        $this->assertCount(7, $roles);

        /* @var $role \BjyAuthorize\Acl\Role */
        foreach ($roles as $role) {
            $this->assertInstanceOf('BjyAuthorize\Acl\Role', $role);
            $this->assertContains(
                $role->getRoleId(),
                array('role1', 'role2', 'role3', 'role4', 'role5', 'role6', 'role7')
            );

            if ('role4' === $role->getRoleId()) {
                $this->assertNotNull($role->getParent());
                $this->assertSame('role3', $role->getParent()->getRoleId());
            } elseif ('role6' === $role->getRoleId() || 'role7' === $role->getRoleId()) {
                $this->assertNotNull($role->getParent());
                $this->assertSame('role5', $role->getParent()->getRoleId());
            } else {
                $this->assertNull($role->getParent());
            }
        }
    }
}
