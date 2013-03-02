<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\View;

use BjyAuthorize\Guard\Route;
use PHPUnit_Framework_TestCase;
use BjyAuthorize\View\RedirectionStrategy;
use Zend\Http\Response;
use Zend\Mvc\Application;

/**
 * UnauthorizedStrategyTest view strategy test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RedirectionStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \BjyAuthorize\View\RedirectionStrategy
     */
    protected $strategy;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->strategy = new RedirectionStrategy();
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::attach
     * @covers \BjyAuthorize\View\RedirectionStrategy::detach
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
        $this->strategy->attach($eventManager);
        $eventManager
            ->expects($this->once())
            ->method('detach')
            ->with($callbackMock)
            ->will($this->returnValue(true));
        $this->strategy->detach($eventManager);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     */
    public function testWillIgnoreUnrecognizedResponse()
    {
        $mvcEvent     = $this->getMock('Zend\\Mvc\\MvcEvent');
        $response     = $this->getMock('Zend\\Stdlib\\ResponseInterface');
        $routeMatch   = $this->getMock('Zend\\Mvc\\Router\\RouteMatch', array(), array(), '', false);

        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Route::ERROR));
        $mvcEvent->expects($this->never())->method('setResponse');

        $this->strategy->onDispatchError($mvcEvent);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     */
    public function testWillIgnoreUnrecognizedErrorType()
    {
        $mvcEvent     = $this->getMock('Zend\\Mvc\\MvcEvent');
        $response     = $this->getMock('Zend\\Http\\Response');
        $routeMatch   = $this->getMock('Zend\\Mvc\\Router\\RouteMatch', array(), array(), '', false);
        $route        = $this->getMock('Zend\\Mvc\\Router\\RouteInterface');

        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $mvcEvent->expects($this->any())->method('getRouter')->will($this->returnValue($route));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue('unknown'));
        $mvcEvent->expects($this->never())->method('setResponse');

        $this->strategy->onDispatchError($mvcEvent);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     */
    public function testWillIgnoreOnExistingResult()
    {
        $mvcEvent     = $this->getMock('Zend\\Mvc\\MvcEvent');
        $response     = $this->getMock('Zend\\Http\\Response');
        $routeMatch   = $this->getMock('Zend\\Mvc\\Router\\RouteMatch', array(), array(), '', false);

        $mvcEvent->expects($this->any())->method('getResult')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Route::ERROR));
        $mvcEvent->expects($this->never())->method('setResponse');

        $this->strategy->onDispatchError($mvcEvent);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     */
    public function testWillIgnoreOnMissingRouteMatch()
    {
        $mvcEvent     = $this->getMock('Zend\\Mvc\\MvcEvent');
        $response     = $this->getMock('Zend\\Http\\Response');

        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Route::ERROR));
        $mvcEvent->expects($this->never())->method('setResponse');

        $this->strategy->onDispatchError($mvcEvent);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     * @covers \BjyAuthorize\View\RedirectionStrategy::setRedirectRoute
     * @covers \BjyAuthorize\View\RedirectionStrategy::setRedirectUri
     */
    public function testWillRedirectToRouteOnSetRoute()
    {
        $this->strategy->setRedirectRoute('redirect/route');
        $this->strategy->setRedirectUri(null);

        $mvcEvent     = $this->getMock('Zend\\Mvc\\MvcEvent');
        $response     = $this->getMock('Zend\\Http\\Response');
        $routeMatch   = $this->getMock('Zend\\Mvc\\Router\\RouteMatch', array(), array(), '', false);
        $route        = $this->getMock('Zend\\Mvc\\Router\\RouteInterface');
        $headers      = $this->getMock('Zend\\Http\\Headers');

        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $mvcEvent->expects($this->any())->method('getRouter')->will($this->returnValue($route));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Route::ERROR));

        $response->expects($this->any())->method('getHeaders')->will($this->returnValue($headers));
        $response->expects($this->once())->method('setStatusCode')->with(302);

        $headers->expects($this->once())->method('addHeaderLine')->with('Location', 'http://www.example.org/');

        $route
            ->expects($this->any())
            ->method('assemble')
            ->with(array(), array('name' => 'redirect/route'))
            ->will($this->returnValue('http://www.example.org/'));

        $mvcEvent->expects($this->once())->method('setResponse')->with($response);

        $this->strategy->onDispatchError($mvcEvent);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     * @covers \BjyAuthorize\View\RedirectionStrategy::setRedirectUri
     */
    public function testWillRedirectToRouteOnSetUri()
    {
        $this->strategy->setRedirectUri('http://www.example.org/');

        $mvcEvent     = $this->getMock('Zend\\Mvc\\MvcEvent');
        $response     = $this->getMock('Zend\\Http\\Response');
        $routeMatch   = $this->getMock('Zend\\Mvc\\Router\\RouteMatch', array(), array(), '', false);
        $route        = $this->getMock('Zend\\Mvc\\Router\\RouteInterface');
        $headers      = $this->getMock('Zend\\Http\\Headers');

        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $mvcEvent->expects($this->any())->method('getRouter')->will($this->returnValue($route));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Route::ERROR));

        $response->expects($this->any())->method('getHeaders')->will($this->returnValue($headers));
        $response->expects($this->once())->method('setStatusCode')->with(302);

        $headers->expects($this->once())->method('addHeaderLine')->with('Location', 'http://www.example.org/');

        $mvcEvent->expects($this->once())->method('setResponse')->with($response);

        $this->strategy->onDispatchError($mvcEvent);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     * @covers \BjyAuthorize\View\RedirectionStrategy::setRedirectUri
     */
    public function testWillRedirectToRouteOnSetUriWithApplicationError()
    {
        $this->strategy->setRedirectUri('http://www.example.org/');

        $mvcEvent     = $this->getMock('Zend\\Mvc\\MvcEvent');
        $response     = $this->getMock('Zend\\Http\\Response');
        $routeMatch   = $this->getMock('Zend\\Mvc\\Router\\RouteMatch', array(), array(), '', false);
        $route        = $this->getMock('Zend\\Mvc\\Router\\RouteInterface');
        $headers      = $this->getMock('Zend\\Http\\Headers');
        $exception    = $this->getMock('BjyAuthorize\\Exception\\UnAuthorizedException');

        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $mvcEvent->expects($this->any())->method('getRouter')->will($this->returnValue($route));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Application::ERROR_EXCEPTION));
        $mvcEvent->expects($this->any())->method('getParam')->with('exception')->will($this->returnValue($exception));

        $response->expects($this->any())->method('getHeaders')->will($this->returnValue($headers));
        $response->expects($this->once())->method('setStatusCode')->with(302);

        $headers->expects($this->once())->method('addHeaderLine')->with('Location', 'http://www.example.org/');

        $mvcEvent->expects($this->once())->method('setResponse')->with($response);

        $this->strategy->onDispatchError($mvcEvent);
    }
}
