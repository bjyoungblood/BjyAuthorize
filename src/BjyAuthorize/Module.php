<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * BjyAuthorize Module
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Module implements
    AutoloaderProviderInterface,
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
        $eventManager = $app->getEventManager();

        // Bind on event, then routing will be finished
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'initBjyAuthorize'));
    }

    /**
     * {@inheritDoc}
     */
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'isAllowed' => function (AbstractPluginManager $pluginManager) {
                    $serviceLocator = $pluginManager->getServiceLocator();
                    /* @var $authorize \BjyAuthorize\Service\Authorize */
                    $authorize = $serviceLocator->get('BjyAuthorize\Service\Authorize');

                    return new View\Helper\IsAllowed($authorize);
                }
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
                'isAllowed' => function (AbstractPluginManager $pluginManager) {
                    $serviceLocator = $pluginManager->getServiceLocator();
                    /* @var $authorize \BjyAuthorize\Service\Authorize */
                    $authorize = $serviceLocator->get('BjyAuthorize\Service\Authorize');

                    return new Controller\Plugin\IsAllowed($authorize);
                }
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
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

    /**
     * Initializes main functionality of BjyAuthorize module
     *
     * @param MvcEvent $e
     */
    public function initBjyAuthorize(MvcEvent $e)
    {
        $app = $e->getApplication();
        /* @var $sm \Zend\ServiceManager\ServiceLocatorInterface */
        $serviceManager = $app->getServiceManager();
        $eventManager = $app->getEventManager();

        $config         = $serviceManager->get('BjyAuthorize\Config');
        $strategy       = $serviceManager->get($config['unauthorized_strategy']);
        $guards         = $serviceManager->get('BjyAuthorize\Guards');

        foreach ($guards as $guard) {
            $eventManager->attach($guard);
        }

        $eventManager->attach($strategy);
    }
}
