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
use BjyAuthorize\Service\GuardsServiceFactory;

/**
 * Test for {@see \BjyAuthorize\Service\GuardsServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>s
 */
class GuardsServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Service\GuardsServiceFactory::createService
     */
    public function testAuthenticationIdentityProviderServiceFactory()
    {
        $factory        = new GuardsServiceFactory();
        $serviceLocator = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $foo            = $this->getMock('BjyAuthorize\\Guard\\GuardInterface');
        $bar            = $this->getMock('BjyAuthorize\\Guard\\GuardInterface');
        $guardsConfig   = array(
            'guards' => array(
                'foo'                         => array(),
                'bar'                         => array(),
                __NAMESPACE__ . '\\MockGuard' => array('option' => 'value'),
            ),
        );

        $serviceLocator
            ->expects($this->any())
            ->method('has')
            ->will($this->returnCallback(function ($serviceName) {
                return in_array($serviceName, array('foo', 'bar'), true);
            }));

        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($serviceName) use ($foo, $bar, $guardsConfig) {
                if ('BjyAuthorize\\Config' === $serviceName) {
                    return $guardsConfig;
                }

                if ('foo' === $serviceName) {
                    return $foo;
                }

                return $bar;
            }));

        $guards = $factory->createService($serviceLocator);

        $this->assertCount(3, $guards);
        $this->assertContains($foo, $guards);
        $this->assertContains($bar, $guards);

        $invokableGuard = array_filter(
            $guards,
            function ($item) {
                return $item instanceof MockGuard;
            }
        );

        $this->assertCount(1, $invokableGuard);

        /* @var $invokableGuard \BjyAuthorizeTest\Service\MockGuard */
        $invokableGuard = array_shift($invokableGuard);

        $this->assertInstanceOf(__NAMESPACE__ . '\\MockGuard', $invokableGuard);

        $this->assertSame(array('option' => 'value'), $invokableGuard->options);
        $this->assertSame($serviceLocator, $invokableGuard->serviceLocator);
    }
}
