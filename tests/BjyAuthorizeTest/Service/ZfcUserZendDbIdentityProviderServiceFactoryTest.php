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
use BjyAuthorize\Service\ZfcUserZendDbIdentityProviderServiceFactory;

/**
 * Test for {@see \BjyAuthorize\Service\ZfcUserZendDbIdentityProviderServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ZfcUserZendDbIdentityProviderServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Service\ZfcUserZendDbIdentityProviderServiceFactory::createService
     */
    public function testCreateService()
    {
        $factory          = new ZfcUserZendDbIdentityProviderServiceFactory();
        $serviceLocator   = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $adapter          = $this->getMock('Zend\\Db\\Adapter\\Adapter', array(), array(), '', false);
        $userService      = $this->getMock('ZfcUser\\Service\\User');

        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->with($this->logicalOr('zfcuser_zend_db_adapter', 'zfcuser_user_service', 'BjyAuthorize\\Config'))
            ->will(
                $this->returnCallback(
                    function ($serviceName) use ($adapter, $userService) {
                        if ('zfcuser_zend_db_adapter' === $serviceName) {
                            return $adapter;
                        }

                        if ('zfcuser_user_service' === $serviceName) {
                            return $userService;
                        }

                        return array('default_role' => 'test_role');
                    }
                )
            );

        $provider = $factory->createService($serviceLocator);

        $this->assertInstanceOf('BjyAuthorize\\Provider\\Identity\\ZfcUserZendDb', $provider);
        $this->assertSame('test_role', $provider->getDefaultRole());
    }
}
