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
    public function testLoadLoadsAclFromCacheAndDoesNotBuildANewAclObject()
    {
        $this->markTestIncomplete('Unable to change the value of $success');
        $success = false;

        $cache = $this->getMockBuilder('\Zend\Cache\Storage\Adapter\Filesystem')
                 ->disableOriginalConstructor()
                 ->getMock();

        $cache->expects($this->once())
              ->method('getItem')
              ->with('bjyauthorize-acl', $success)
              ->will($this->returnCallback(function() use (&$success) {
                  $success = true;
                  return $this->getMock('\Zend\Permissions\Acl\Acl');
              }));

        $serviceManager = new ServiceManager();

        $serviceManager->setFactory(
            'BjyAuthorize\Provider\Identity\ProviderInterface',
            function () {
                return $this->getMock('BjyAuthorize\Provider\Identity\ProviderInterface');
            }
        );

        $serviceManager->setFactory(
            'BjyAuthorize\Cache',
            function () use ($cache) {
                return $cache;
            }
        );

        $authorize = new Authorize(array('cache_key' => 'bjyauthorize-acl'), $serviceManager);
        $authorize->load();
    }

    /**
     * @covers  \BjyAuthorize\Service\Authorize::load
     */
    public function testLoadWritesAclToCacheIfCacheIsEnabledButAclIsNotStoredInCache()
    {
        $this->markTestIncomplete('Unable to change the value of $success');
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

        $authorize->load();
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
