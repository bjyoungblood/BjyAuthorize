<?php

namespace BjyAuthorize;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\EventManager\Event;

class Module implements
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    public function onBootstrap(Event $e)
    {
        $app        = $e->getTarget();
        $sm         = $app->getServiceManager();
        $service    = $sm->get('BjyAuthorize\Service\Authorize');
        $strategy   = $sm->get('BjyAuthorize\View\UnauthorizedStrategy');

        foreach ($service->getGuards() as $guard) {
            $app->events()->attach('route', array($guard, 'onRoute'), -1000);
        }

        $app->events()->attach($strategy);
    }

    public function getServiceConfiguration()
    {
        return array(
            'factories' => array(
                'BjyAuthorize\Service\Authorize' => 'BjyAuthorize\Service\AuthorizeFactory',
                'BjyAuthorize\Provider\Identity\ZfcUserZendDb' => function ($sm) {
                    $adapter = $sm->get('zfcuser_zend_db_adapter');
                    $provider = new Provider\Identity\ZfcUserZendDb($adapter);
                    $provider->setUserService($sm->get('zfcuser_user_service'));
                    return $provider;
                },
                'BjyAuthorize\View\UnauthorizedStrategy' => function ($sm) {
                    $template = $sm->get('BjyAuthorize\Service\Authorize')->getTemplate();
                    $strategy = new View\UnauthorizedStrategy;
                    $strategy->setTemplate($template);
                    return $strategy;
                },
                'BjyAuthorize\Controller\Plugin\IsAllowed' => function ($sm) {
                    $plugin = new Controller\Plugin\IsAllowed();
                    $plugin->setAuthorizeService($sm->get('BjyAuthorize\Service\Authorize'));
                    return $plugin;
                },

                'BjyAuthorize\View\Helper\IsAllowed' => function ($sm) {
                    $helper = new View\Helper\IsAllowed();
                    $helper->setAuthorizeService($sm->get('BjyAuthorize\Service\Authorize'));
                    return $helper;
                },

                'BjyAuthorize\Provider\Role\ZendDb' => function ($sm) {
                    $provider = new Provider\Role\ZendDb;
                    $provider->setAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                    return $provider;
                }
            ),
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
