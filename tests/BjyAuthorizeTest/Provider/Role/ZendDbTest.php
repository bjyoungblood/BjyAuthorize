<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Provider\Role;

use BjyAuthorize\Provider\Role\ZendDb;
use PHPUnit_Framework_TestCase;
use BjyAuthorize\Provider\Role\ObjectRepositoryProvider;

/**
 * {@see \BjyAuthorize\Provider\Role\ZendDb} test
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ZendDbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \BjyAuthorize\Provider\Role\ObjectRepositoryProvider
     */
    private $provider;

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceLocator;

    /**
     * @covers \BjyAuthorize\Provider\Role\ZendDb::__construct
     */
    protected function setUp()
    {
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->provider       = new ZendDb(array(), $this->serviceLocator);
    }

    /**
     * @covers \BjyAuthorize\Provider\Role\ZendDb::getRoles
     */
    public function testGetRoles()
    {
        $this->markTestIncomplete();
    }
}
