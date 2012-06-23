<?php

namespace BjyAuthorize\Guard\Controller;

class Controller
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

            $resourceName = self::getResourceName($controller, $action);
            $this->rules[$resourceName] = $rule['roles'];
        }
    }

    public function isAllowed($controller, $action = null)
    {
        $this->securityService->isAllowed();
    }

    public function getResources()
    {
        $resources = array();
        foreach ($this->rules as $resource => $roles) {
            $resources[] = $resource;
        }

        return $resources;
    }

    public static function getResourceName($controller, $action = null)
    {
        if (isset($action)) {
            $resourceName = sprintf('controller/%s', $controller);
        } else {
            $resourceName = sprintf('controller/%s:%s', $controller, $action);
        }
    }
}
