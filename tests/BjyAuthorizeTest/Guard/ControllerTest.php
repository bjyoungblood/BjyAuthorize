<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Guard;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Guard\Controller;
use Zend\Mvc\MvcEvent;

/**
 * Controller Guard test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ControllerTest extends PHPUnit_Framework_TestCase
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
     * @var Controller
     */
    protected $controllerGuard;

    /**
     * {@inheritDoc}
     *
     * @covers \BjyAuthorize\Guard\Controller::__construct
     */
    public function setUp()
    {
        parent::setUp();

        $this->serviceLocator  = $locator = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $this->authorize = $authorize = $this->getMock('BjyAuthorize\\Service\\Authorize', array(), array(), '', false);
        $this->controllerGuard = new Controller(array(), $this->serviceLocator);

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
     * @covers \BjyAuthorize\Guard\Controller::attach
     * @covers \BjyAuthorize\Guard\Controller::detach
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
        $this->controllerGuard->attach($eventManager);
        $eventManager
            ->expects($this->once())
            ->method('detach')
            ->with($callbackMock)
            ->will($this->returnValue(true));
        $this->controllerGuard->detach($eventManager);
    }

    /**
     * @dataProvider controllersRulesProvider
     *
     * @covers \BjyAuthorize\Guard\Controller::__construct
     * @covers \BjyAuthorize\Guard\Controller::getResources
     * @covers \BjyAuthorize\Guard\Controller::getRules
     *
     * @param array     $rule
     * @param int       $expectedCount
     * @param string    $resource
     * @param array     $roles
     */
    public function testGetResourcesGetRules($rule, $expectedCount, $resource, $roles)
    {
        $controller = new Controller(array($rule), $this->serviceLocator);

        $resources = $controller->getResources();

        $this->assertCount($expectedCount, $resources);
        $this->assertContains($resource, $resources);

        $rules = $controller->getRules();

        $this->assertCount($expectedCount, $rules['allow']);
        $this->assertContains(array($roles, $resource), $rules['allow']);
    }

    /**
     * @covers \BjyAuthorize\Guard\Controller::getResourceName
     */
    public function testGetResourceName()
    {
        $this->assertSame('controller/test1:action1', $this->controllerGuard->getResourceName('test1', 'action1'));
        $this->assertSame('controller/test2', $this->controllerGuard->getResourceName('test2'));
    }

    /**
     * @covers \BjyAuthorize\Guard\Controller::onDispatch
     */
    public function testOnDispatchWithValidController()
    {
        $event = $this->createMvcEvent('test-controller');
        $event->getTarget()->getEventManager()->expects($this->never())->method('trigger');
        $this
            ->authorize
            ->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue(function ($resource) {
                return $resource === 'controller/test-controller';
            }));

        $this->assertNull($this->controllerGuard->onDispatch($event), 'Does not stop event propagation');
    }

    /**
     * @covers \BjyAuthorize\Guard\Controller::onDispatch
     */
    public function testOnDispatchWithValidControllerAndAction()
    {
        $event = $this->createMvcEvent('test-controller', 'test-action');
        $event->getTarget()->getEventManager()->expects($this->never())->method('trigger');
        $this
            ->authorize
            ->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue(function ($resource) {
                return $resource === 'controller/test-controller:test-action';
            }));

        $this->assertNull($this->controllerGuard->onDispatch($event), 'Does not stop event propagation');
    }

    /**
     * @covers \BjyAuthorize\Guard\Controller::onDispatch
     */
    public function testOnDispatchWithValidControllerAndMethod()
    {
        $event = $this->createMvcEvent('test-controller', null, 'PUT');
        $event->getTarget()->getEventManager()->expects($this->never())->method('trigger');
        $this
            ->authorize
            ->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue(function ($resource) {
                return $resource === 'controller/test-controller:put';
            }));

        $this->assertNull($this->controllerGuard->onDispatch($event), 'Does not stop event propagation');
    }

    /**
     * @covers \BjyAuthorize\Guard\Controller::onDispatch
     */
    public function testOnDispatchWithValidControllerAction()
    {
        $event = $this->createMvcEvent('test-controller', 'test-action');
        $event->getTarget()->getEventManager()->expects($this->never())->method('trigger');
        $this
            ->authorize
            ->expects($this->any())
            ->method('isAllowed')
            ->with('controller/test-controller')
            ->will($this->returnValue(true));

        $this->assertNull($this->controllerGuard->onDispatch($event), 'Does not stop event propagation');
    }

    /**
     * @covers \BjyAuthorize\Guard\Controller::onDispatch
     */
    public function testOnDispatchWithInvalidResource()
    {
        $event = $this->createMvcEvent('test-controller', 'test-action');
        $this->authorize->expects($this->any())->method('getIdentity')->will($this->returnValue('admin'));
        $this
            ->authorize
            ->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue(false));
        $event->expects($this->once())->method('setError')->with(Controller::ERROR);
        $event->expects($this->exactly(3))->method('setParam')->with(
            $this->callback(function ($key) {
                return in_array($key, array('identity', 'controller', 'action'));
            }),
            $this->callback(function ($val) {
                return in_array($val, array('admin', 'test-controller', 'test-action'));
            })
        );
        $event
            ->getTarget()
            ->getEventManager()
            ->expects($this->once())
            ->method('trigger')
            ->with(MvcEvent::EVENT_DISPATCH_ERROR, $event);

        $this->assertNull($this->controllerGuard->onDispatch($event), 'Does not stop event propagation');
    }

    /**
     * @param string|null $controller
     * @param string|null $action
     * @param string|null $method
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Zend\Mvc\MvcEvent
     */
    private function createMvcEvent($controller = null, $action = null, $method = null)
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
        $request->expects($this->any())->method('getMethod')->will($this->returnValue($method));
        $routeMatch
            ->expects($this->any())
            ->method('getParam')
            ->will($this->returnCallback(function ($param) use ($controller, $action) {
                if ($param === 'controller') {
                    return $controller;
                }

                if ($param === 'action') {
                    return $action;
                }

                return null;
            }));

        return $event;
    }

    /**
     * Return a set of rules, with expected resources count, expected resource names
     * and expected output rules
     *
     * @return array
     */
    public function controllersRulesProvider()
    {
        return array(
            array(
                array(
                    'controller' => 'test-controller',
                    'action'     => 'test-action',
                    'roles'      => array(
                        'admin',
                        'user',
                    ),
                ),
                1,
                'controller/test-controller:test-action',
                array('admin', 'user')
            ),
            array(
                array(
                    'controller' => 'test2-controller',
                    'roles'      => array(
                        'admin2',
                        'user2',
                    ),
                ),
                1,
                'controller/test2-controller',
                array('admin2', 'user2')
            ),
            array(
                array(
                    'controller' => 'test3-controller',
                    'action'     => 'test3-action',
                    'roles'      => 'admin3'
                ),
                1,
                'controller/test3-controller:test3-action',
                array('admin3')
            ),
            array(
                array(
                    'controller' => 'test4-controller',
                    'action'     => array(
                        'test4-action',
                        'test5-action',
                    ),
                    'roles'      => array(
                        'admin4',
                        'user3',
                    ),
                ),
                2,
                'controller/test4-controller:test4-action',
                array('admin4', 'user3')
            ),
            array(
                array(
                    'controller' => 'test4-controller',
                    'action'     => array(
                        'test4-action',
                        'test5-action',
                    ),
                    'roles'      => array(
                        'admin4',
                        'user3',
                    ),
                ),
                2,
                'controller/test4-controller:test5-action',
                array('admin4', 'user3')
            ),
            array(
                array(
                    'controller' => 'test5-controller',
                    'action'     => null,
                    'roles'      => 'user4'
                ),
                1,
                'controller/test5-controller',
                array('user4')
            ),
            array(
                array(
                    'controller' => array(
                        'test6-controller',
                        'test7-controller',
                    ),
                    'action'     => null,
                    'roles'      => 'user5'
                ),
                2,
                'controller/test6-controller',
                array('user5')
            ),
            array(
                array(
                    'controller' => array(
                        'test6-controller',
                        'test7-controller',
                    ),
                    'action'     => null,
                    'roles'      => 'user5'
                ),
                2,
                'controller/test7-controller',
                array('user5')
            ),
            array(
                array(
                    'controller' => array(
                        'test6-controller',
                        'test7-controller',
                    ),
                    'action'     => array(
                        'test6-action',
                        'test7-action',
                    ),
                    'roles'      => array(
                        'admin5',
                        'user6',
                    ),
                ),
                4,
                'controller/test6-controller:test6-action',
                array('admin5', 'user6')
            ),
            array(
                array(
                    'controller' => array(
                        'test6-controller',
                        'test7-controller',
                    ),
                    'action'     => array(
                        'test6-action',
                        'test7-action',
                    ),
                    'roles'      => array(
                        'admin5',
                        'user6',
                    ),
                ),
                4,
                'controller/test7-controller:test7-action',
                array('admin5', 'user6')
            )
        );
    }
}
