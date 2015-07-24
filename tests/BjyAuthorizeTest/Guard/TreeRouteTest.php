<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Guard;

use BjyAuthorize\Acl\Role;
use BjyAuthorize\Guard\TreeRoute;
use BjyAuthorize\Service\Authorize;
use PHPUnit_Framework_TestCase;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

/**
 * Tree Route Guard test
 *
 * @author Marvin Feldmann <breyndot.echse@gmail.com>
 */
class TreeRouteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Authorize
     */
    protected $authorize;

    /**
     * @var TreeRoute
     */
    protected $treeRouteGuard;
    
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $cache = $this->getMockBuilder('Zend\Cache\Storage\Adapter\Filesystem')
                      ->disableOriginalConstructor()
                      ->getMock();

        $cache
            ->expects($this->once())
            ->method('getItem')
            ->will(
                $this->returnCallback(
                    function ($key, & $success) {
                        $success = false;
                        return null;
                    }
                )
            );


        $serviceManager = new ServiceManager();
        $this->treeRouteGuard = $treeRouteGuard = new TreeRoute(
            array(
                array(
                    'route' => 'test/route',
                    'roles' => array(
                        'admin',
                        'user',
                    ),
                ),
                array(
                    'route' => 'test/route/admin/only',
                    'roles' => array(
                        'admin',
                    ),
                ),
                array(
                    'route' => 'test2-route',
                    'roles' => array(
                        'admin2',
                        'user2',
                    ),
                ),
                array(
                    'route' => 'test3-route',
                    'roles' => 'admin3'
                ),
            ),
            $serviceManager
        );


        $identityRole = new Role('user');
        $identityProvider = $this->getMock('BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider', array(), array(), '', false);
        $identityProvider
            ->expects($this->any())
            ->method('getIdentityRoles')
            ->will($this->returnValue(array($identityRole)));


        $roleProvider = $this->getMock('BjyAuthorize\Provider\Role\Config');
        $roleProvider
            ->expects($this->once())
            ->method('getRoles')
            ->will(
                $this->returnValue(
                    array(
                        $identityRole,
                        new Role('admin'),
                        new Role('admin2'),
                        new Role('user2'),
                        new Role('admin3'))
                )
            );

        $serviceManager->setService('BjyAuthorize\Provider\Identity\ProviderInterface', $identityProvider);
        $serviceManager->setService('BjyAuthorize\RoleProviders', array($roleProvider));
        $serviceManager->setService('BjyAuthorize\ResourceProviders', array());
        $serviceManager->setService('BjyAuthorize\RuleProviders', array());
        $serviceManager->setService('BjyAuthorize\Guards', array($treeRouteGuard));
        $serviceManager->setService('BjyAuthorize\Cache', $cache);
        $serviceManager->setService('BjyAuthorize\CacheKeyGenerator', function() {
            return 'bjyauthorize_acl';
        });

        $authorize = new Authorize(array('cache_key' => 'bjyauthorize-acl'), $serviceManager);
        $serviceManager->setService('BjyAuthorize\Service\Authorize', $authorize);
    }

    /**
     * @covers \BjyAuthorize\Guard\Route::__construct
     * @covers \BjyAuthorize\Guard\TreeRoute::onRoute
     */
    public function testOnRouteWithValidParentRoute()
    {
        $event = $this->createMvcEvent('test/route/foo/bar');
        $event->getTarget()->getEventManager()->expects($this->never())->method('trigger');

        $this->assertNull($this->treeRouteGuard->onRoute($event), 'Does not stop event propagation');
    }

    /**
     * @covers \BjyAuthorize\Guard\Route::__construct
     * @covers \BjyAuthorize\Guard\TreeRoute::onRoute
     */
    public function testOnRouteWithValidParentRouteAndRevokedRole()
    {
        $event = $this->createMvcEvent('test/route/admin/only/test');
        $event->expects($this->once())->method('setError')->with(TreeRoute::ERROR);

        $event->expects($this->at(3))->method('setParam')->with('route', 'test/route/admin/only/test');
        $event->expects($this->at(4))->method('setParam')->with('identity', 'bjyauthorize-identity');
        $event->expects($this->at(5))->method('setParam')->with(
            'exception',
            $this->isInstanceOf('BjyAuthorize\Exception\UnAuthorizedException')
        );

        $event
            ->getTarget()
            ->getEventManager()
            ->expects($this->once())
            ->method('trigger')
            ->with(MvcEvent::EVENT_DISPATCH_ERROR, $event);

        $this->assertNull($this->treeRouteGuard->onRoute($event), 'Does not stop event propagation');
    }

    /**
     * @param string|null $route
     * @return \PHPUnit_Framework_MockObject_MockObject|MvcEvent
     */
    protected function createMvcEvent($route = null)
    {
        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');
        $application  = $this->getMock('Zend\Mvc\Application', array(), array(), '', false);
        $event        = $this->getMock('Zend\Mvc\MvcEvent');
        $routeMatch   = $this->getMock('Zend\Mvc\Router\\RouteMatch', array(), array(), '', false);
        $request      = $this->getMock('Zend\Http\Request');

        $event->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $event->expects($this->any())->method('getRequest')->will($this->returnValue($request));
        $event->expects($this->any())->method('getTarget')->will($this->returnValue($application));
        $application->expects($this->any())->method('getEventManager')->will($this->returnValue($eventManager));
        $routeMatch->expects($this->any())->method('getMatchedRouteName')->will($this->returnValue($route));

        return $event;
    }
}
