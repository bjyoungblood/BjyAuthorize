<?php

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Service\Authorize;
use \PHPUnit_Framework_TestCase;
use Zend\Di\ServiceLocator;
use Zend\ServiceManager\ServiceManager;

/**
 * Test for {@see \BjyAuthorize\Service\Authorize}
 *
 * @author Christian Bergau <cbergau86@gmail.com>
 */
class AuthorizeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers  \BjyAuthorize\Service\Authorize::load
     */
    public function testLoadUsesCacheIfCacheIsEnabled()
    {
        $providerInterface = $this->getMock('BjyAuthorize\Provider\Identity\ProviderInterface');

        $serviceLocator = $this->getMockBuilder('\Zend\ServiceManager\ServiceLocatorInterface')
                               ->disableOriginalConstructor()
                               ->getMock();
        $serviceLocator->expects($this->once())
                       ->method('get')
                       ->with('BjyAuthorize\Provider\Identity\ProviderInterface')
                       ->will($this->returnValue($providerInterface));

        $authorize = new Authorize(array(), $serviceLocator);
        $cache = $this->getMockBuilder('\Zend\Cache\Storage\Adapter\Filesystem')
                      ->disableOriginalConstructor()
                      ->getMock();

        $cacheKey = 'bjyauthorize-acl';

        $cache->expects($this->once())
              ->method('getItem')
              ->with($cacheKey)
              ->will($this->returnValue($this->getMock('\Zend\Permissions\Acl\Acl')));

        $authorize->setCache($cache)
                  ->setCacheKey($cacheKey)
                  ->setCacheEnabled(true);

        $authorize->load();
    }

    /**
     * @covers  \BjyAuthorize\Service\Authorize::load
     */
    public function testLoadWritesAclToCacheIfCacheIsEnabledButAclIsNotStoredInCache()
    {
        $serviceLocator = $this->getServiceManagerForLoadAcl();

        $authorize = new Authorize(array(), $serviceLocator);

        $cache = $this->getMockBuilder('\Zend\Cache\Storage\Adapter\Filesystem')
                 ->disableOriginalConstructor()
                 ->getMock();

        $cacheKey = 'bjyauthorize-acl';

        // getItem will return null, so we expect that after loading acl, the data will be stored into cache
        $cache->expects($this->once())
              ->method('getItem')
              ->with($cacheKey)
              ->will($this->returnValue(null));

        $cache->expects($this->once())
              ->method('setItem')
              ->will($this->returnValue(null));

        $authorize->setCache($cache)
                  ->setCacheKey($cacheKey)
                  ->setCacheEnabled(true);

        $authorize->load();
    }

    public function testUseCacheReturnsFalseIfFlagIsSetToFalse()
    {
        $cache = $this->getMockBuilder('\Zend\Cache\Storage\Adapter\Filesystem')
                 ->disableOriginalConstructor()
                 ->getMock();

        $authorize = new Authorize(array(), new ServiceManager());
        $authorize->setCache($cache);
        $authorize->setCacheKey('bjyauthorize-acl');
        $authorize->setCacheEnabled(false);

        $this->assertFalse($authorize->useCache());
    }

    public function testUseCacheReturnsFalseIfCacheIsNull()
    {
        $authorize = new Authorize(array(), new ServiceManager());
        $authorize->setCache(null);
        $authorize->setCacheKey('bjyauthorize-acl');
        $authorize->setCacheEnabled(true);

        $this->assertFalse($authorize->useCache());
    }

    public function testUseCacheReturnsTrueIfCacheIsSetAndFlagIsTrue()
    {
        $cache = $this->getMockBuilder('\Zend\Cache\Storage\Adapter\Filesystem')
                      ->disableOriginalConstructor()
                      ->getMock();

        $authorize = new Authorize(array(), new ServiceManager());
        $authorize->setCache($cache);
        $authorize->setCacheKey('bjyauthorize-acl');
        $authorize->setCacheEnabled(true);

        $this->assertTrue($authorize->useCache());
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManagerForLoadAcl()
    {
        $serviceLocator = new ServiceManager();
        $serviceLocator->setFactory(
            'BjyAuthorize\Provider\Identity\ProviderInterface',
            function () {
                return $this->getMock('BjyAuthorize\Provider\Identity\ProviderInterface');
            }
        );
        $serviceLocator->setFactory(
            'BjyAuthorize\RoleProviders',
            function () {
                return $this->getMock('BjyAuthorize\Service\RoleProvidersServiceFactory');
            }
        );
        $serviceLocator->setFactory(
            'BjyAuthorize\ResourceProviders',
            function () {
                return $this->getMock('BjyAuthorize\Service\ResourceProvidersServiceFactory');
            }
        );
        $serviceLocator->setFactory(
            'BjyAuthorize\RuleProviders',
            function () {
                return $this->getMock('BjyAuthorize\Service\RuleProvidersServiceFactory');
            }
        );
        $serviceLocator->setFactory(
            'BjyAuthorize\Guards',
            function () {
                return $this->getMock('BjyAuthorize\Service\GuardsServiceFactory');
            }
        );
        return $serviceLocator;
    }
}
