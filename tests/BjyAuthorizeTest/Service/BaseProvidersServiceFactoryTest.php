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

/**
 * Test for {@see \BjyAuthorize\Service\ResourceProvidersServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class BaseProvidersServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Service\BaseProvidersServiceFactory::createService
     */
    public function testCreateService()
    {
        $factory        = $this->getMockForAbstractClass('BjyAuthorize\\Service\\BaseProvidersServiceFactory');
        $serviceLocator = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $foo            = $this->getMock('BjyAuthorize\\Provider\\Resource\\ProviderInterface');
        $bar            = $this->getMock('BjyAuthorize\\Provider\\Resource\\ProviderInterface');
        $config         = array(
            'providers' => array(
                'foo'                         => array(),
                'bar'                         => array(),
                __NAMESPACE__ . '\\MockProvider' => array('option' => 'value'),
            ),
        );

        $serviceLocator
            ->expects($this->any())
            ->method('has')
            ->will(
                $this->returnCallback(
                    function ($serviceName) {
                        return in_array($serviceName, array('foo', 'bar'), true);
                    }
                )
            );

        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->with($this->logicalOr('BjyAuthorize\\Config', 'foo', 'bar'))
            ->will(
                $this->returnCallback(
                    function ($serviceName) use ($foo, $bar, $config) {
                        if ('BjyAuthorize\\Config' === $serviceName) {
                            return $config;
                        }

                        if ('foo' === $serviceName) {
                            return $foo;
                        }

                        return $bar;
                    }
                )
            );

        $providers = $factory->createService($serviceLocator);

        $this->assertCount(3, $providers);
        $this->assertContains($foo, $providers);
        $this->assertContains($bar, $providers);

        $invokableProvider = array_filter(
            $providers,
            function ($item) {
                return $item instanceof MockProvider;
            }
        );

        $this->assertCount(1, $invokableProvider);

        /* @var $invokableGuard \BjyAuthorizeTest\Service\MockProvider */
        $invokableProvider = array_shift($invokableProvider);

        $this->assertInstanceOf(__NAMESPACE__ . '\\MockProvider', $invokableProvider);

        $this->assertSame(array('option' => 'value'), $invokableProvider->options);
        $this->assertSame($serviceLocator, $invokableProvider->serviceLocator);
    }
}
