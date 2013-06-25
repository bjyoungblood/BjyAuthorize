<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Service\Authorize;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;

/**
 * Test for {@see \BjyAuthorize\Service\Authorize}
 *
 * @author Christian Bergau <cbergau86@gmail.com>
 */
class AuthorizeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Service\Authorize::load
     */
    public function testLoadLoadsAclFromCacheAndDoesNotBuildANewAclObject()
    {
        $acl = $this->getMock('Zend\Permissions\Acl\Acl');

        $cache = $this->getMockBuilder('Zend\Cache\Storage\Adapter\Filesystem')
                      ->disableOriginalConstructor()
                      ->getMock();

        $cache
            ->expects($this->once())
            ->method('getItem')
            ->will(
                $this->returnCallback(
                    function ($key, & $success) use ($acl) {
                        $success = true;

                        return $acl;
                    }
                )
            );

        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'BjyAuthorize\Provider\Identity\ProviderInterface',
            $this->getMock('BjyAuthorize\Provider\Identity\ProviderInterface')
        );
        $serviceManager->setService('BjyAuthorize\Cache', $cache);

        $authorize = new Authorize(array('cache_key' => 'bjyauthorize-acl'), $serviceManager);
        $authorize->load();

        $this->assertSame($acl, $authorize->getAcl());
    }

    /**
     * @covers \BjyAuthorize\Service\Authorize::load
     */
    public function testLoadWritesAclToCacheIfCacheIsEnabledButAclIsNotStoredInCache()
    {
        $cache = $this->getMockBuilder('Zend\Cache\Storage\Adapter\Filesystem')
                      ->disableOriginalConstructor()
                      ->getMock();

        $cache->expects($this->once())->method('getItem');
        $cache->expects($this->once())->method('setItem');

        $serviceLocator = new ServiceManager();
        $serviceLocator->setService('BjyAuthorize\Cache', $cache);
        $serviceLocator->setService(
            'BjyAuthorize\Provider\Identity\ProviderInterface',
            $this->getMock('BjyAuthorize\Provider\Identity\ProviderInterface')
        );
        $serviceLocator->setService(
            'BjyAuthorize\RoleProviders',
            $this->getMock('BjyAuthorize\Service\RoleProvidersServiceFactory')
        );
        $serviceLocator->setService(
            'BjyAuthorize\ResourceProviders',
            $this->getMock('BjyAuthorize\Service\ResourceProvidersServiceFactory')
        );
        $serviceLocator->setService(
            'BjyAuthorize\RuleProviders',
            $this->getMock('BjyAuthorize\Service\RuleProvidersServiceFactory')
        );
        $serviceLocator->setService(
            'BjyAuthorize\Guards',
            $this->getMock('BjyAuthorize\Service\GuardsServiceFactory')
        );

        $authorize = new Authorize(array('cache_key' => 'acl'), $serviceLocator);
        $authorize->load();
    }
}
