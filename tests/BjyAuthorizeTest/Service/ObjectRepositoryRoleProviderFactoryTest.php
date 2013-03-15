<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Service;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Service\ObjectRepositoryRoleProviderFactory;

/**
 * {@see \BjyAuthorize\Service\ObjectRepositoryRoleProviderFactory} test
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ObjectRepositoryRoleProviderFactoryTest extends PHPUnit_Framework_TestCase
{
    private $locator;
    private $entityManager;
    private $repository;
    private $factory;

    protected function setUp()
    {
        $this->locator       = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->entityManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository    = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->factory       = new ObjectRepositoryRoleProviderFactory();
    }

    /**
     * @covers \BjyAuthorize\Service\ObjectRepositoryRoleProviderFactory::createService
     */
    public function testCreateService()
    {
        $testClassName = 'TheTestClass';

        $config = array(
            'role_providers' => array(
                'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => array(
                    'role_entity_class' => $testClassName,
                    'object_manager'    => 'doctrine.entitymanager.orm_default',
                ),
            ),
        );

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with($testClassName)
            ->will($this->returnValue($this->repository));

        $this->locator->expects($this->at(0))
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $this->locator->expects($this->at(1))
            ->method('get')
            ->with('doctrine.entitymanager.orm_default')
            ->will($this->returnValue($this->entityManager));

        $this->assertInstanceOf(
            'BjyAuthorize\Provider\Role\ObjectRepositoryProvider',
            $this->factory->createService($this->locator)
        );
    }
}
