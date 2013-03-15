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
use BjyAuthorize\Service\ConfigServiceFactory;

/**
 * Test for {@see \BjyAuthorize\Service\ConfigServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Service\ConfigServiceFactory::createService
     */
    public function testCreateService()
    {
        $factory        = new ConfigServiceFactory();
        $serviceLocator = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');

        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue(array('bjyauthorize' => array('foo' => 'bar'))));

        $this->assertSame(array('foo' => 'bar'), $factory->createService($serviceLocator));
    }
}
