<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 * @package        Zend_Service
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Service\AuthorizeAwareInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Initializer that injects a {@see \BjyAuthorize\Service\Authorize} in
 * objects that are instances of {@see \BjyAuthorize\Service\AuthorizeAwareInterface}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class AuthorizeAwareServiceInitializer implements InitializerInterface
{
    /**
     * {@inheritDoc}
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if (! $instance instanceof AuthorizeAwareInterface) {
            return;
        }

        /* @var $authorize \BjyAuthorize\Service\Authorize */
        $authorize = $serviceLocator->get('BjyAuthorize\Service\Authorize');

        $instance->setAuthorizeService($authorize);
    }
}
