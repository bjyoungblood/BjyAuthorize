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
use BjyAuthorize\Service\UnauthorizedStrategyServiceFactory;

/**
 * Test for {@see \BjyAuthorize\Service\UnauthorizedStrategyServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class UnauthorizedStrategyServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Service\UnauthorizedStrategyServiceFactory::createService
     */
    public function testCreateService()
    {
        $factory          = new UnauthorizedStrategyServiceFactory();
        $serviceLocator   = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $config           = array(
            'template' => 'foo/bar',
        );

        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $strategy = $factory->createService($serviceLocator);

        $this->assertInstanceOf('BjyAuthorize\\View\\UnauthorizedStrategy', $strategy);
        $this->assertSame('foo/bar', $strategy->getTemplate());
    }
}
