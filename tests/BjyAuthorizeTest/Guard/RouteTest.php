<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Guard;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Guard\Route;
use Zend\Mvc\MvcEvent;

/**
 * Route Guard test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RouteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $serviceLocator;

    /**
     * @var \BjyAuthorize\Service\Authorize|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorize;

    /**
     * @var Route
     */
    protected $routeGuard;

    /**
     * {@inheritDoc}
     *
     * @covers \BjyAuthorize\Guard\Route::__construct
     */
    public function setUp()
    {
        parent::setUp();

        $this->serviceLocator = $locator = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $this->authorize = $authorize = $this->getMock('BjyAuthorize\\Service\\Authorize', array(), array(), '', false);
        $this->routeGuard = new Route(array(), $this->serviceLocator);

        $this
            ->serviceLocator
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($name) use ($authorize) {
                if ($name === 'BjyAuthorize\Service\Authorize') {
                    return $authorize;
                }

                throw new \UnexpectedValueException();
            }));
    }

    /**
     * @covers \BjyAuthorize\Guard\Route::attach
     * @covers \BjyAuthorize\Guard\Route::detach
     */
    public function testAttachDetach()
    {
        $eventManager = $this->getMock('Zend\\EventManager\\EventManagerInterface');
        $callbackMock = $this->getMock('Zend\\Stdlib\\CallbackHandler', array(), array(), '', false);
        $eventManager
            ->expects($this->once())
            ->method('attach')
            ->with()
            ->will($this->returnValue($callbackMock));
        $this->routeGuard->attach($eventManager);
        $eventManager
            ->expects($this->once())
            ->method('detach')
            ->with($callbackMock)
            ->will($this->returnValue(true));
        $this->routeGuard->detach($eventManager);
    }

    /**
     * @covers \BjyAuthorize\Guard\Route::__construct
     * @covers \BjyAuthorize\Guard\Route::getResources
     * @covers \BjyAuthorize\Guard\Route::getRules
     */
    public function testGetResourcesGetRules()
    {
        $controller = new Route(
            array(
                 array(
                     'route' => 'test/route',
                     'roles' => array(
                         'admin',
                         'user',
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
            $this->serviceLocator
        );

        $resources = $controller->getResources();

        $this->assertCount(3, $resources);
        $this->assertContains('route/test/route', $resources);
        $this->assertContains('route/test2-route', $resources);
        $this->assertContains('route/test3-route', $resources);

        $rules = $controller->getRules();

        $this->assertCount(3, $rules['allow']);
        $this->assertContains(
            array(array('admin', 'user'), 'route/test/route'),
            $rules['allow']
        );
        $this->assertContains(
            array(array('admin2', 'user2'), 'route/test2-route'),
            $rules['allow']
        );
        $this->assertContains(
            array(array('admin3'), 'route/test3-route'),
            $rules['allow']
        );
    }

    /**
     * @covers \BjyAuthorize\Guard\Route::onRoute
     */
    public function testOnRouteWithValidRoute()
    {
        $event = $this->createMvcEvent('test-route');
        $event->getTarget()->getEventManager()->expects($this->never())->method('trigger');
        $this
            ->authorize
            ->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue(function ($resource) {
                return $resource === 'route/test-route';
            }));

        $this->assertNull($this->routeGuard->onRoute($event), 'Does not stop event propagation');
    }

    /**
     * @covers \BjyAuthorize\Guard\Route::onRoute
     */
    public function testOnRouteWithInvalidResource()
    {
        $event = $this->createMvcEvent('test-route');
        $this->authorize->expects($this->any())->method('getIdentity')->will($this->returnValue('admin'));
        $this
            ->authorize
            ->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue(false));
        $event->expects($this->once())->method('setError')->with(Route::ERROR);
        $event->expects($this->exactly(2))->method('setParam')->with(
            $this->callback(function ($key) {
                return in_array($key, array('identity', 'route'));
            }),
            $this->callback(function ($val) {
                return in_array($val, array('admin', 'test-route'));
            })
        );
        $event
            ->getTarget()
            ->getEventManager()
            ->expects($this->once())
            ->method('trigger')
            ->with(MvcEvent::EVENT_DISPATCH_ERROR, $event);

        $this->assertNull($this->routeGuard->onRoute($event), 'Does not stop event propagation');
    }

    /**
     * @param string|null $route
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Zend\Mvc\MvcEvent
     */
    private function createMvcEvent($route = null)
    {
        $eventManager = $this->getMock('Zend\\EventManager\\EventManagerInterface');
        $application  = $this->getMock('Zend\\Mvc\\Application', array(), array(), '', false);
        $event        = $this->getMock('Zend\\Mvc\\MvcEvent');
        $routeMatch   = $this->getMock('Zend\\Mvc\\Router\\RouteMatch', array(), array(), '', false);
        $request      = $this->getMock('Zend\\Http\\Request');

        $event->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $event->expects($this->any())->method('getRequest')->will($this->returnValue($request));
        $event->expects($this->any())->method('getTarget')->will($this->returnValue($application));
        $application->expects($this->any())->method('getEventManager')->will($this->returnValue($eventManager));
        $routeMatch->expects($this->any())->method('getMatchedRouteName')->will($this->returnValue($route));

        return $event;
    }
}
