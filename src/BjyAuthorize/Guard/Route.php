<?php

namespace BjyAuthorize\Guard;

use BjyAuthorize\Provider\Rule\ProviderInterface as RuleProviderInterface;
use BjyAuthorize\Provider\Resource\ProviderInterface as ResourceProviderInterface;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Mvc\MvcEvent;

class Route implements RuleProviderInterface, ResourceProviderInterface
{
    protected $securityService;

    protected $rules = array();


    public function setOptions(array $rules, $security)
    {
        $this->securityService = $security;

        foreach ($rules as $rule)
        {
            if (!is_array($rule['roles'])) {
                $rule['roles'] = array($rule['roles']);
            }

            $resourceName = 'route/'.$rule['route'];
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

    public static function onRoute(MvcEvent $e)
    {
        $app        = $e->getTarget();
        $service    = $app->getServiceManager()->get('BjyAuthorize\Service\Authorize');
        $match      = $app->getMvcEvent()->getRouteMatch();
        $routeName  = $match->getMatchedRouteName();
        $allowed = $service->isAllowed('route/' . $routeName);

        if (!$allowed) {
            $e->setError('error-unauthorized-route')
              ->setParam('route', $routeName)
              ->setParam('identity', $service->getIdentity());

            $app->getEventManager()->trigger('dispatch.error', $e);
        }
    }
}
