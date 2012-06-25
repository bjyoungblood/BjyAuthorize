<?php

namespace BjyAuthorize\Service;

use BjyAuthorize\Provider\Role\ProviderInterface as RoleProvider;
use BjyAuthorize\Provider\Resource\ProviderInterface as ResourceProvider;
use BjyAuthorize\Provider\Rule\ProviderInterface as RuleProvider;
use BjyAuthorize\Provider\Identity\ProviderInterface as IdentityProvider;
use Zend\ServiceManager\ServiceLocatorInterface;

class Authorize
{
    protected $acl;
    protected $roleProviders = array();
    protected $resourceProviders = array();
    protected $ruleProviders = array();
    protected $identityProvider;
    protected $identity;
    protected $loaded = false;

    const TYPE_ALLOW = 'allow';
    const TYPE_DENY = 'deny';

    public function __construct(array $config = array(), ServiceLocatorInterface $serviceLocator)
    {
        $this->acl = new \Zend\Acl\Acl;
        $this->sl = $serviceLocator;

        if (isset($config['role_providers'])) {
            foreach ($config['role_providers'] as $class => $options) {
                $this->addRoleProvider(new $class($options));
            }
        }

        if (isset($config['resource_providers'])) {
            foreach ($config['resource_providers'] as $class => $options) {
                $this->addResourceProvider(new $class($options));
            }
        }

        if (isset($config['rule_providers'])) {
            foreach ($config['rule_providers'] as $class => $options) {
                $this->addRuleProvider(new $class($options));
            }
        }

        if (isset($config['identity_provider'])) {
            $this->setIdentityProvider($serviceLocator->get($config['identity_provider']));
            $this->identityProvider->setDefaultRole($config['default_role']);
        }

        if (isset($config['guards'])) {

        }
    }

    public function addRoleProvider(RoleProvider $provider)
    {
        $this->roleProviders[] = $provider;
        return $this;
    }

    public function addResourceProvider(ResourceProvider $provider)
    {
        $this->resourceProviders[] = $provider;
        return $this;
    }

    public function addRuleProvider(RuleProvider $provider)
    {
        $this->ruleProviders[] = $provider;
        return $this;
    }

    public function setIdentityProvider(IdentityProvider $provider)
    {
        $this->identityProvider = $provider;
        return $this;
    }

    public function getIdentity()
    {
        return 'bjyauthorize-identity';
    }

    public function isAllowed($resource, $privilege)
    {
        if (!$this->loaded) {
            $this->load();
        }

        return $this->acl->isAllowed($this->getIdentity(), $resource, $privilege);
    }

    protected function load()
    {
        foreach ($this->roleProviders as $i) {
            $this->addRoles($i->getRoles());
        }

        foreach ($this->resourceProviders as $provider) {
            $this->loadResource($provider->getResources(), null);
        }

        foreach ($this->ruleProviders as $provider) {
            $rules = $provider->getRules();
            if (isset($rules['allow'])) {
                foreach ($rules['allow'] as $rule) {
                    $this->loadRule($rule, static::TYPE_ALLOW);
                }
            }

            if (isset($rules['deny'])) {
                foreach ($rules['deny'] as $rule) {
                    $this->loadRule($rule, static::TYPE_DENY);
                }
            }
        }

        $parentRoles = $this->identityProvider->getIdentityRoles();
        $this->acl->addRole($this->getIdentity(), $parentRoles);
    }

    protected function addRoles($roles)
    {
        foreach ($roles as $i) {
            if ($i->getParent() === null) {
                $this->acl->addRole($i, $i->getParent());
            } else {
                $this->acl->addRole($i);
            }
        }
    }

    protected function loadResource(array $resources, $parent = null)
    {
        foreach ($resources as $key => $value) {
            if (is_string($key)) {
                $key = new \Zend\Acl\Resource\GenericResource($key);
            }

            if (is_array($value)) {
                $this->acl->addResource($key, $parent);
                $this->loadResource($value, $key);
            } else {
                $this->acl->addResource($key, $parent);
            }
        }
    }

    protected function loadRule(array $rule, $type)
    {
        $roles = $resources = $privileges = $assertion = null;

        if (count($rule) === 4) {
            list($roles, $resources, $privileges, $assertion) = $rule;
        } else if (count($rule) === 3) {
            list($roles, $resources, $privileges) = $rule;
        } else if (count($rule) === 2) {
            list($roles, $resources) = $rule;
        } else {
            throw new \InvalidArgumentException('Invalid rule definition: ' . print_r($rule, true));
        }

        if ($type === static::TYPE_ALLOW) {
            $this->acl->allow($roles, $resources, $privileges, $assertion);
        } else {
            $this->acl->deny($roles, $resources, $privileges, $assertion);
        }
    }
}
