<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\View;

use PHPUnit_Framework_TestCase;
use BjyAuthorize\Service\DoctrineEntityRoleProviderFactory;

/**
 * {@see \BjyAuthorize\Service\DoctrineEntityRoleProviderFactory} test
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class DoctrineEntityRoleProviderFactoryTest extends PHPUnit_Framework_TestCase
{
    private $locator;
    private $entityManager;
    private $repository;
    private $factory;

    protected function setUp()
    {
        $this->locator       = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->entityManager = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $this->repository    = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $this->factory = new DoctrineEntityRoleProviderFactory();
    }

    /**
     * @covers \BjyAuthorize\Service\DoctrineEntityRoleProviderFactory::createService
     */
    public function testCreateService()
    {
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(DoctrineEntityRoleProviderFactory::DEFAULT_ROLE_ENTITY_CLASS))
            ->will($this->returnValue($this->repository));

        $this->locator->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('Config'))
            ->will($this->returnValue(array()));

        $this->locator->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('doctrine.entitymanager.orm_default'))
            ->will($this->returnValue($this->entityManager));


        $this->assertInstanceOf(
            'BjyAuthorize\Provider\Role\DoctrineEntity',
            $this->factory->createService($this->locator)
        );
    }

    /**
     * @covers \BjyAuthorize\Service\DoctrineEntityRoleProviderFactory::createService
     */
    public function testCreateServiceWithConfig()
    {
        $testClassName = 'TheTestClass';

        $config = array(
            'bjy_authorize' => array(
                'role_providers' => array(
                    'BjyAuthorize\Provider\Role\DoctrineEntity' => array(
                        'role_entity_class' => $testClassName,
                    ),
                ),
            ),
        );

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo($testClassName))
            ->will($this->returnValue($this->repository));

        $this->locator->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('Config'))
            ->will($this->returnValue($config));

        $this->locator->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('doctrine.entitymanager.orm_default'))
            ->will($this->returnValue($this->entityManager));


        $this->assertInstanceOf(
            'BjyAuthorize\Provider\Role\DoctrineEntity',
            $this->factory->createService($this->locator)
        );
    }
}
