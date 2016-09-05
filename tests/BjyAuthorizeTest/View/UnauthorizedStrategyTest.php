<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\View;

use BjyAuthorize\Exception\UnAuthorizedException;
use PHPUnit_Framework_TestCase;
use BjyAuthorize\View\UnauthorizedStrategy;
use Zend\Http\Response;
use Zend\Mvc\Application;
use Zend\View\Model\ModelInterface;

/**
 * UnauthorizedStrategyTest view strategy test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class UnauthorizedStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var UnauthorizedStrategy
     */
    protected $strategy;

    /**
     * {@inheritDoc}
     *
     * @covers \BjyAuthorize\View\UnauthorizedStrategy::__construct
     */
    public function setUp()
    {
        parent::setUp();

        $this->strategy = new UnauthorizedStrategy('template/name');
    }

    /**
     * @covers \BjyAuthorize\View\UnauthorizedStrategy::attach
     * @covers \BjyAuthorize\View\UnauthorizedStrategy::detach
     */
    public function testAttachDetach()
    {
        $eventManager = $this->getMockBuilder('Zend\\EventManager\\EventManagerInterface')
            ->getMock();

        $callbackMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['__invoke'])
            ->getMock();

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
     * @covers \BjyAuthorize\View\UnauthorizedStrategy::setTemplate
     * @covers \BjyAuthorize\View\UnauthorizedStrategy::getTemplate
     */
    public function testGetSetTemplate()
    {
        $this->assertSame('template/name', $this->strategy->getTemplate());
        $this->strategy->setTemplate('other/template');
        $this->assertSame('other/template', $this->strategy->getTemplate());
    }

    /**
     * @covers \BjyAuthorize\View\UnauthorizedStrategy::onDispatchError
     */
    public function testOnDispatchErrorWithGenericUnAuthorizedException()
    {
        $exception = $this->getMock(UnAuthorizedException::class);
        $viewModel = $this->getMock('Zend\\View\\Model\\ModelInterface');
        $mvcEvent  = $this->getMock('Zend\\Mvc\\MvcEvent');

        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Application::ERROR_EXCEPTION));
        $mvcEvent->expects($this->any())->method('getViewModel')->will($this->returnValue($viewModel));
        $mvcEvent
            ->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnCallback(
                    function ($name) use ($exception) {
                        return $name === 'exception' ? $exception : null;
                    }
                )
            );

        $test = $this;

        $viewModel
            ->expects($this->once())
            ->method('addChild')
            ->will(
                $this->returnCallback(
                    function (ModelInterface $model) use ($test) {
                        // using a return callback because of a bug in HHVM
                        if ('template/name' !== $model->getTemplate()) {
                            throw new \UnexpectedValueException('Template name does not match expectations!');
                        }
                    }
                )
            );
        $mvcEvent
            ->expects($this->once())
            ->method('setResponse')
            ->will(
                $this->returnCallback(
                    function (Response $response) use ($test) {
                        // using a return callback because of a bug in HHVM
                        if (403 !== $response->getStatusCode()) {
                            throw new \UnexpectedValueException('Response code not match expectations!');
                        }
                    }
                )
            );

        $this->assertNull($this->strategy->onDispatchError($mvcEvent));
    }

    /**
     * @covers \BjyAuthorize\View\UnauthorizedStrategy::onDispatchError
     */
    public function testIgnoresUnknownExceptions()
    {
        $exception = $this->getMock(\Exception::class);
        $viewModel = $this->getMock('Zend\\View\\Model\\ModelInterface');
        $mvcEvent  = $this->getMock('Zend\\Mvc\\MvcEvent');

        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Application::ERROR_EXCEPTION));
        $mvcEvent->expects($this->any())->method('getViewModel')->will($this->returnValue($viewModel));
        $mvcEvent
            ->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnCallback(
                    function ($name) use ($exception) {
                        return $name === 'exception' ? $exception : null;
                    }
                )
            );

        $viewModel->expects($this->never())->method('addChild');
        $mvcEvent->expects($this->never())->method('setResponse');

        $this->assertNull($this->strategy->onDispatchError($mvcEvent));
    }

    /**
     * @covers \BjyAuthorize\View\UnauthorizedStrategy::onDispatchError
     */
    public function testIgnoresUnknownErrors()
    {
        $viewModel = $this->getMock('Zend\\View\\Model\\ModelInterface');
        $mvcEvent  = $this->getMock('Zend\\Mvc\\MvcEvent');

        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue('unknown'));
        $mvcEvent->expects($this->any())->method('getViewModel')->will($this->returnValue($viewModel));

        $viewModel->expects($this->never())->method('addChild');
        $mvcEvent->expects($this->never())->method('setResponse');

        $this->assertNull($this->strategy->onDispatchError($mvcEvent));
    }

    /**
     * @covers \BjyAuthorize\View\UnauthorizedStrategy::onDispatchError
     */
    public function testIgnoresOnExistingResponse()
    {
        $response = $this->getMock('Zend\\Stdlib\\ResponseInterface');
        $viewModel = $this->getMock('Zend\\View\\Model\\ModelInterface');
        $mvcEvent  = $this->getMock('Zend\\Mvc\\MvcEvent');

        $mvcEvent->expects($this->any())->method('getResult')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getViewModel')->will($this->returnValue($viewModel));

        $viewModel->expects($this->never())->method('addChild');
        $mvcEvent->expects($this->never())->method('setResponse');

        $this->assertNull($this->strategy->onDispatchError($mvcEvent));
    }
}
