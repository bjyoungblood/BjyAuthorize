<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\View;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Service\AuthenticationDoctrineEntityFactory;

/**
 * {@see \BjyAuthorize\Service\AuthenticationDoctrineEntityFactory} test
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class AuthenticationDoctrineEntityFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $locator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $authService;

    /**
     * @var \BjyAuthorize\Service\AuthenticationDoctrineEntityFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->locator       = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->authService   = $this->getMock('Zend\Authentication\AuthenticationService');

        $this->locator->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('zfcuser_auth_service'))
            ->will($this->returnValue($this->authService));

        $this->locator->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('Config'));

        $this->factory = new AuthenticationDoctrineEntityFactory();
    }

    /**
     * @covers \BjyAuthorize\Service\AuthenticationDoctrineEntityFactory::createService
     */
    public function testCreateServiceWithConfig()
    {
        $this->assertInstanceOf(
            '\BjyAuthorize\Provider\Identity\AuthenticationDoctrineEntity',
            $this->factory->createService($this->locator)
        );
    }
}
