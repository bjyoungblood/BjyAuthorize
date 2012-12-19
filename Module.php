<?php

namespace BjyAuthorize;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

class Module implements
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    public function onBootstrap(EventInterface $e)
    {
        $app        = $e->getTarget();
        $config     = $app->getConfig();
        $sm         = $app->getServiceManager();
        $service    = $sm->get('BjyAuthorize\Service\Authorize');
        $strategy   = $sm->get($config['bjyauthorize']['unauthorized_strategy']);

        foreach ($service->getGuards() as $guard) {
            $app->getEventManager()->attach($guard);
        }

        $app->getEventManager()->attach($strategy);
    }

    public function getServiceConfig()
    {
        return array(
            'initializers' => array(
                function ($instance, $sm) {
                    if ($instance instanceof Service\AuthorizeAwareInterface) {
                        $instance->setAuthorizeService($sm->get('BjyAuthorize\Service\Authorize'));
                    }
                }
            ),
            'factories' => array(
                'BjyAuthorize\Service\Authorize' => 'BjyAuthorize\Service\AuthorizeFactory',

                'BjyAuthorize\Provider\Identity\ZfcUserZendDb' => function ($sm) {
                    $adapter = $sm->get('zfcuser_zend_db_adapter');
                    $provider = new Provider\Identity\ZfcUserZendDb($adapter);
                    $provider->setUserService($sm->get('zfcuser_user_service'));
                    return $provider;
                },

                'BjyAuthorize\Provider\Identity\ZfcUserDoctrine' => function ($sm) {
                    $em = $sm->get('doctrine.entitymanager.orm_default');
                    $provider = new Provider\Identity\ZfcUserDoctrine($em);
                    $provider->setUserService($sm->get('zfcuser_user_service'));
                    return $provider;
                },

                'BjyAuthorize\View\UnauthorizedStrategy' => function ($sm) {
                    $template = $sm->get('BjyAuthorize\Service\Authorize')->getTemplate();
                    $strategy = new View\UnauthorizedStrategy;
                    $strategy->setTemplate($template);
                    return $strategy;
                },

                'BjyAuthorize\Provider\Role\ZendDb' => function ($sm) {
                    $provider = new Provider\Role\ZendDb;
                    $provider->setAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                    return $provider;
                },

                'BjyAuthorize\Provider\Role\Doctrine' => function ($sm) {
                    $provider = new Provider\Role\Doctrine;
                    return $provider;
                },
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'isAllowed' => function($sm) {
                    $sm = $sm->getServiceLocator(); // get the main SM instance
                    $helper = new View\Helper\IsAllowed();
                    $helper->setAuthorizeService($sm->get('BjyAuthorize\Service\Authorize'));
                    return $helper;
                }
            ),
        );
    }

    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'isAllowed' => function($sm) {
                    $sm = $sm->getServiceLocator(); // get the main SM instance
                    $helper = new Controller\Plugin\IsAllowed();
                    $helper->setAuthorizeService($sm->get('BjyAuthorize\Service\Authorize'));
                    return $helper;
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
