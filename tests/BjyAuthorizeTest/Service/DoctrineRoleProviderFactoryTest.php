<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\View;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Service\DoctrineRoleProviderFactory;

/**
 * {@see \BjyAuthorize\Service\DoctrineRoleProviderFactory} test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class DoctrineRoleProviderFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \BjyAuthorize\Service\DoctrineRoleProviderFactory::createService
     */
    public function testCreateService()
    {
        $locator       = $this->getMock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $entityManager = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);

        $locator->expects($this->once())->method('get')->will($this->returnValue($entityManager));

        $factory = new DoctrineRoleProviderFactory();

        $this->assertInstanceOf('BjyAuthorize\\Provider\\Role\\Doctrine', $factory->createService($locator));
    }
}
