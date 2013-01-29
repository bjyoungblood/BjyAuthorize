<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\View;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Service\ZfcUserDoctrineEntityFactory;

/**
 * {@see \BjyAuthorize\Service\ZfcUserDoctrineEntityFactory} test
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ZfcUserDoctrineEntityFactoryTest extends PHPUnit_Framework_TestCase
{
    private $locator;
    private $authService;
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

        $this->factory = new ZfcUserDoctrineEntityFactory();
    }

    /**
     * @covers \BjyAuthorize\Service\ZfcUserDoctrineEntityFactory::createService
     */
    public function testCreateServiceWithConfig()
    {
        $this->assertInstanceOf(
            '\BjyAuthorize\Provider\Identity\ZfcUserDoctrineEntity',
            $this->factory->createService($this->locator)
        );
    }
}
