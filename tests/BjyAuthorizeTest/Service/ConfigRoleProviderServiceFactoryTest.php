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
use BjyAuthorize\Service\ConfigRoleProviderServiceFactory;

/**
 * Test for {@see \BjyAuthorize\Service\ConfigRoleProviderServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigRoleProviderServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Service\ConfigRoleProviderServiceFactory::createService
     */
    public function testCreateService()
    {
        $factory          = new ConfigRoleProviderServiceFactory();
        $serviceLocator   = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $config           = array(
            'role_providers' => array(
                'BjyAuthorize\Provider\Role\Config' => array(),
            ),
        );

        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $guard = $factory->createService($serviceLocator);

        $this->assertInstanceOf('BjyAuthorize\\Provider\\Role\\Config', $guard);
    }
}
