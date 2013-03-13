<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 * @package        Zend_Service
 */

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Provider\Resource\ProviderInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Marco Pivetta <ocramius@gmail.com>s
 */
class MockResourceProvider implements ProviderInterface
{
    /**
     * @var array
     */
    public $options;

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    public $serviceLocator;

    /**
     * @param array                                        $options
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function __construct(array $options, ServiceLocatorInterface $serviceLocator)
    {
        $this->options        = $options;
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritDoc}
     */
    public function getResources()
    {
        return array();
    }
}
