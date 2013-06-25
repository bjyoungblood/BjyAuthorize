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

use BjyAuthorize\Service\ConfigRuleProviderServiceFactory;
use PHPUnit_Framework_TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\ConfigRuleProviderServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigRuleProviderServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Service\ConfigRuleProviderServiceFactory::createService
     */
    public function testCreateService()
    {
        $factory          = new ConfigRuleProviderServiceFactory();
        $serviceLocator   = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $config           = array('rule_providers' => array('BjyAuthorize\\Provider\\Rule\\Config' => array()));

        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\\Config')
            ->will($this->returnValue($config));

        $this->assertInstanceOf('BjyAuthorize\\Provider\\Rule\\Config', $factory->createService($serviceLocator));
    }
}
