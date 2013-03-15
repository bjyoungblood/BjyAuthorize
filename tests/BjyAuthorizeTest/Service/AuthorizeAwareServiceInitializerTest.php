<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 * @package        Zend_Service
 */

namespace BjyAuthorizeTest\Service;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Service\AuthorizeAwareServiceInitializer;

/**
 * Test for {@see \BjyAuthorize\Service\AuthorizeAwareServiceInitializer}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class AuthorizeAwareServiceInitializerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorize;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $locator;

    /**
     * @var \BjyAuthorize\Service\AuthorizeAwareServiceInitializer
     */
    protected $initializer;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->authorize   = $this->getMock('BjyAuthorize\\Service\\Authorize', array(), array(), '', false);
        $this->locator     = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $this->initializer = new AuthorizeAwareServiceInitializer();

        $this->locator->expects($this->any())->method('get')->will($this->returnValue($this->authorize));
    }

    /**
     * @covers \BjyAuthorize\Service\AuthorizeAwareServiceInitializer::initialize
     */
    public function testInitializeWithAuthorizeAwareObject()
    {
        $awareObject = $this->getMock('BjyAuthorize\\Service\\AuthorizeAwareInterface');

        $awareObject->expects($this->once())->method('setAuthorizeService')->with($this->authorize);

        $this->initializer->initialize($awareObject, $this->locator);
    }

    /**
     * @covers \BjyAuthorize\Service\AuthorizeAwareServiceInitializer::initialize
     */
    public function testInitializeWithSimpleObject()
    {
        $awareObject = $this->getMock('stdClass', array('setAuthorizeService'));

        $awareObject->expects($this->never())->method('setAuthorizeService');

        $this->initializer->initialize($awareObject, $this->locator);
    }
}
