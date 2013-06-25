<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Service\CacheFactory;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;

/**
 * PHPUnit tests for {@see \BjyAuthorize\Service\CacheFactory}
 *
 * @author Christian Bergau <cbergau86@gmail.com>
 */
class CacheFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Service\CacheFactory::createService
     */
    public function testCreateService()
    {
        $serviceLocator = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $config         = array(
            'cache_options' => array(
                'adapter'   => array(
                    'name' => 'memory',
                ),
                'plugins'   => array(
                    'serializer',
                )
            )
        );

        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $factory = new CacheFactory();

        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\Memory', $factory->createService($serviceLocator));
    }
}
