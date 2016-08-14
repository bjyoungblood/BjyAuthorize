<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize;

use BjyAuthorize\Controller\Plugin;
use BjyAuthorize\Guard\AbstractGuard;
use BjyAuthorize\View\Helper;
use BjyAuthorize\View\UnauthorizedStrategy;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

/**
 * BjyAuthorize Module
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Module implements
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ControllerPluginProviderInterface,
    ViewHelperProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function onBootstrap(EventInterface $event)
    {
        /* @var $app \Zend\Mvc\ApplicationInterface */
        $app = $event->getTarget();
        /* @var $sm \Zend\ServiceManager\ServiceLocatorInterface */
        $serviceManager = $app->getServiceManager();
        $config = $serviceManager->get('BjyAuthorize\Config');
        /** @var UnauthorizedStrategy $strategy */
        $strategy = $serviceManager->get($config['unauthorized_strategy']);
        /** @var AbstractGuard[] $guards */
        $guards = $serviceManager->get('BjyAuthorize\Guards');

        foreach ($guards as $guard) {
            $guard->attach($app->getEventManager());
        }

        $strategy->attach($app->getEventManager());
    }

    /**
     * {@inheritDoc}
     */
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'isAllowed' => Helper\IsAllowedFactory::class,
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'isAllowed' => Plugin\IsAllowedFactory::class
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
