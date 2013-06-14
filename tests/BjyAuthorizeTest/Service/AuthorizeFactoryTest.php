<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Service\AuthorizeFactory;
use \PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;

/**
 * Test for {@see \BjyAuthorize\Service\AuthorizeAwareServiceInitializer}
 *
 * @author Christian Bergau <cbergau86@gmail.com>
 */
class AuthorizeFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers  \BjyAuthorize\Service\AuthorizeFactory::createService
     */
    public function testCreateServiceSetCacheOptionsIfCacheIsEnabledAndAdapterOptionsAreProvided()
    {
        $serviceLocator = new ServiceManager();
        $serviceLocator->setFactory('BjyAuthorize\Config', function() {
            return array(
                'cache_enabled'         => true,
                'cache_options'         => array(
                    'adapter'   => array(
                        'name' => 'filesystem',
                    ),
                ),
                'cache_key'             => 'bjyauthorize_acl'
            );
        });
        $serviceLocator->setFactory('Application', function() {
            $application = $this->getMockBuilder('Zend\Mvc\Application')
                                ->disableOriginalConstructor()
                                ->getMock();
            $application->expects($this->once())
                        ->method('getRequest')
                        ->will($this->returnValue(
                            $this->getMock('Zend\Http\PhpEnvironment\Request')
                        ));
            return $application;
        });

        $authorizeFactory = new AuthorizeFactory();

        $authorize = $authorizeFactory->createService($serviceLocator);

        $this->assertTrue($authorize->isCacheEnabled());
        $this->assertEquals('bjyauthorize_acl', $authorize->getCacheKey());
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\Filesystem', $authorize->getCache());
    }

    /**
     * @expectedException   \Exception
     */
    public function testCreateServiceThrowsAnExceptionIfCacheIsEnabledButOptionsAreNotProvided()
    {
        $serviceLocator = new ServiceManager();
        $serviceLocator->setFactory('BjyAuthorize\Config', function() {
                return array(
                    'cache_enabled'         => true,
                    'cache_key'             => 'bjyauthorize_acl'
                );
            });
        $serviceLocator->setFactory('Application', function() {
                $application = $this->getMockBuilder('Zend\Mvc\Application')
                               ->disableOriginalConstructor()
                               ->getMock();
                $application->expects($this->once())
                ->method('getRequest')
                ->will($this->returnValue(
                            $this->getMock('Zend\Http\PhpEnvironment\Request')
                        ));
                return $application;
            });

        $authorizeFactory = new AuthorizeFactory();

        // Expect exception here
        $authorizeFactory->createService($serviceLocator);
    }
}
