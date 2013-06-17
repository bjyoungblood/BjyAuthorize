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
        $serviceLocator = new ServiceManager();

        $serviceLocator->setService('BjyAuthorize\Config', array('cache_options' => array(
            'adapter'   => array(
                'name' => 'memory',
            ),
            'plugins'   => array(
                'serializer',
            )
        )));

        $factory = new CacheFactory();
        $cache   = $factory->createService($serviceLocator);

        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\Memory', $cache);
    }
}
