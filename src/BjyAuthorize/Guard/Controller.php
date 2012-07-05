<?php

namespace BjyAuthorize\Guard;

use BjyAuthorize\Provider\Rule\ProviderInterface as RuleProviderInterface;
use BjyAuthorize\Provider\Resource\ProviderInterface as ResourceProviderInterface;
use Zend\Acl\Resource\GenericResource;
use Zend\Mvc\MvcEvent;

class Controller implements RuleProviderInterface, ResourceProviderInterface
{
    protected $securityService;

    protected $rules = array();

    public function __construct(array $rules, $security)
    {
        $this->securityService = $security;

        foreach ($rules as $rule)
        {
            if (!is_array($rule['roles'])) {
                $rule['roles'] = array($rule['roles']);
            }

            $resourceName = static::getResourceName($rule['controller'], isset($rule['action']) ? $rule['action'] : null);
            $this->rules[$resourceName] = $rule['roles'];
        }
    }

    public function getResources()
    {
        $resources = array();
        foreach ($this->rules as $resource => $roles) {
            $resources[] = $resource;
        }

        return $resources;
    }

    public function getRules()
    {
        $rules = array();
        foreach ($this->rules as $resource => $roles) {
            $rules[] = array($roles, $resource);
        }

        return array('allow' => $rules);
    }

    public static function getResourceName($controller, $action = null)
    {
        if (!isset($action)) {
            $resourceName = sprintf('controller/%s', $controller);
        } else {
            $resourceName = sprintf('controller/%s:%s', $controller, $action);
        }

        return $resourceName;
    }

    public static function onRoute(MvcEvent $e)
    {
        $app        = $e->getTarget();
        $service    = $app->getServiceManager()->get('BjyAuthorize\Service\Authorize');
        $match      = $app->getMvcEvent()->getRouteMatch();
        $controller = $match->getParam('controller');
        $action     = $match->getParam('action');

        $controllerResource = sprintf('controller/%s', $controller);
        $actionResource = sprintf('controller/%s:%s', $controller, $action);

        $allowed = $service->isAllowed($controllerResource) || $service->isAllowed($actionResource);

        if (!$allowed) {
            $e->setError('error-unauthorized-controller')
                ->setParam('identity', $service->getIdentity())
                ->setParam('controller', $controller)
                ->setParam('action', $action);

            $app->getEventManager()->trigger('dispatch.error', $e);
        }
    }
}
