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
use BjyAuthorize\Service\TreeRouteGuardServiceFactory;

/**
 * Test for {@see \BjyAuthorize\Service\TreeRouteGuardServiceFactory}
 *
 * @author Marvin Feldmann <breyndot.echse@gmail.com>
 */
class TreeRouteGuardServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Service\TreeRouteGuardServiceFactory::createService
     */
    public function testCreateService()
    {
        $factory          = new TreeRouteGuardServiceFactory();
        $serviceLocator   = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $config           = array(
            'guards' => array(
                'BjyAuthorize\Guard\TreeRoute' => array(),
            ),
        );
        $router = $this->getMock('Zend\Mvc\Router\Http\TreeRouteStack');

        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(
                array(
                    array('BjyAuthorize\Config', $config),
                    array('Router', $router)
                )
            ));

        $guard = $factory->createService($serviceLocator);

        $this->assertInstanceOf('BjyAuthorize\Guard\TreeRoute', $guard);
    }
}
