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
use BjyAuthorize\Service\ZfcUserDoctrineMongoODMIdentityProviderServiceFactory;

/**
 * Test for {@see \BjyAuthorize\Service\ZfcUserDoctrineMongoODMIdentityProviderServiceFactory}
 *
 * @author Mat Wright <mat@bstechnologies.com>
 */
class ZfcUserDoctrineMongoODMIdentityProviderServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Service\ZfcUserDoctrineMongoODMIdentityProviderServiceFactory::createService
     */
    public function testCreateService()
    {
        $factory          = new ZfcUserDoctrineMongoODMIdentityProviderServiceFactory();
        $serviceLocator   = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $guard = $factory->createService($serviceLocator);
        $this->assertInstanceOf('BjyAuthorize\\Provider\\Identity\\ZfcUserDoctrineMongoODM', $guard);
    }
}
