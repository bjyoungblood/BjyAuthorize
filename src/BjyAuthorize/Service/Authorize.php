<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Provider\Role\ProviderInterface as RoleProvider;
use BjyAuthorize\Provider\Resource\ProviderInterface as ResourceProvider;
use BjyAuthorize\Provider\Rule\ProviderInterface as RuleProvider;
use BjyAuthorize\Provider\Identity\ProviderInterface as IdentityProvider;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Exception\InvalidArgumentException;
use Zend\Permissions\Acl\Resource\GenericResource;
use BjyAuthorize\Acl\Role;
use BjyAuthorize\Guard\GuardInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Authorize service
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Authorize
{
    /**
     * @var Acl
     */
    protected $acl;

    /**
     * @var RoleProvider[]
     */
    protected $roleProviders = array();

    /**
     * @var ResourceProvider[]
     */
    protected $resourceProviders = array();

    /**
     * @var RuleProvider[]
     */
    protected $ruleProviders = array();

    /**
     * @var IdentityProvider
     */
    protected $identityProvider;

    /**
     * @var GuardInterface[]
     */
    protected $guards = array();

    protected $template = 'error/403';

    protected $loaded = false;

    protected $serviceLocator;

    const TYPE_ALLOW = 'allow';

    const TYPE_DENY = 'deny';

    /**
     * @param array                   $config
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(array $config, ServiceLocatorInterface $serviceLocator)
    {
        $this->acl = new Acl;
        $this->serviceLocator  = $serviceLocator;

        if (isset($config['template'])) {
            $this->template = $config['template'];
        }

        if (isset($config['role_providers'])) {
            foreach ($config['role_providers'] as $class => $options) {
                $this->addRoleProvider(new $class($options, $serviceLocator));
            }
        }

        if (isset($config['resource_providers'])) {
            foreach ($config['resource_providers'] as $class => $options) {
                $this->addResourceProvider(new $class($options, $serviceLocator));
            }
        }

        if (isset($config['rule_providers'])) {
            foreach ($config['rule_providers'] as $class => $options) {
                $this->addRuleProvider(new $class($options, $serviceLocator));
            }
        }

        if (isset($config['identity_provider'])) {
            $this->setIdentityProvider($serviceLocator->get($config['identity_provider']));
            $this->identityProvider->setDefaultRole($config['default_role']);
        }

        if (isset($config['guards'])) {
            foreach ($config['guards'] as $class => $options) {
                $this->addGuard(new $class($options, $serviceLocator));
            }
        }
    }

    /**
     * @param RoleProvider $provider
     *
     * @return self
     */
    public function addRoleProvider(RoleProvider $provider)
    {
        $this->roleProviders[] = $provider;

        return $this;
    }

    /**
     * @param ResourceProvider $provider
     *
     * @return self
     */
    public function addResourceProvider(ResourceProvider $provider)
    {
        $this->resourceProviders[] = $provider;

        return $this;
    }

    /**
     * @param RuleProvider $provider
     *
     * @return self
     */
    public function addRuleProvider(RuleProvider $provider)
    {
        $this->ruleProviders[] = $provider;

        return $this;
    }

    /**
     * @param IdentityProvider $provider
     *
     * @return self
     */
    public function setIdentityProvider(IdentityProvider $provider)
    {
        $this->identityProvider = $provider;

        return $this;
    }

    /**
     * @return IdentityProvider
     */
    public function getIdentityProvider()
    {
        return $this->identityProvider;
    }

    /**
     * @param GuardInterface $guard
     *
     * @return self
     */
    public function addGuard(GuardInterface $guard)
    {
        $this->guards[] = $guard;

        if ($guard instanceof ResourceProvider) {
            $this->addResourceProvider($guard);
        }

        if ($guard instanceof RuleProvider) {
            $this->addRuleProvider($guard);
        }

        return $this;
    }

    /**
     * @return GuardInterface[]
     */
    public function getGuards()
    {
        return $this->guards;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return 'bjyauthorize-identity';
    }

    /**
     * @return Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * @param string|ResourceInterface $resource
     * @param string                   $privilege
     *
     * @return bool
     */
    public function isAllowed($resource, $privilege = null)
    {
        if (!$this->loaded) {
            $this->load();
        }

        try {
            return $this->acl->isAllowed($this->getIdentity(), $resource, $privilege);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Initializes the service
     */
    protected function load()
    {
        foreach ($this->roleProviders as $provider) {
            $this->addRoles($provider->getRoles());
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

        $parentRoles = $this->getIdentityProvider()->getIdentityRoles();
        $this->acl->addRole($this->getIdentity(), $parentRoles);

        $this->loaded = true;
    }

    /**
     * @param \Zend\Permissions\Acl\Role\RoleInterface[] $roles
     */
    protected function addRoles($roles)
    {
        if (!is_array($roles)) {
            $roles = array($roles);
        }

        /* @var $role Role */
        foreach ($roles as $role) {
            if ($this->acl->hasRole($role)) {
                continue;
            }

            if ($role->getParent() !== null) {
                $this->addRoles($role->getParent());
                $this->acl->addRole($role, $role->getParent());
            } else {
                $this->acl->addRole($role);
            }
        }
    }

    /**
     * @param string[]|\Zend\Permissions\Acl\Resource\ResourceInterface[] $resources
     * @param mixed|null                                                  $parent
     */
    protected function loadResource(array $resources, $parent = null)
    {
        foreach ($resources as $key => $value) {
            if (is_string($key)) {
                $key = new GenericResource($key);
            } elseif (is_int($key)) {
                $key = new GenericResource($value);
            }

            if (is_array($value)) {
                $this->acl->addResource($key, $parent);
                $this->loadResource($value, $key);
            } else {
                $this->acl->addResource($key, $parent);
            }
        }
    }

    /**
     * @param mixed $rule
     * @param mixed $type
     *
     * @throws \InvalidArgumentException
     */
    protected function loadRule(array $rule, $type)
    {
        $privileges = $assertion = null;

        if (count($rule) === 4) {
            list($roles, $resources, $privileges, $assertion) = $rule;
            $assertion = $this->serviceLocator->get($assertion);
        } elseif (count($rule) === 3) {
            list($roles, $resources, $privileges) = $rule;
        } elseif (count($rule) === 2) {
            list($roles, $resources) = $rule;
        } else {
            throw new \InvalidArgumentException('Invalid rule definition: ' . print_r($rule, true));
        }

        if (is_string($assertion)) {
            $assertion = $this->serviceLocator->get($assertion);
        }

        if ($type === static::TYPE_ALLOW) {
            $this->acl->allow($roles, $resources, $privileges, $assertion);
        } else {
            $this->acl->deny($roles, $resources, $privileges, $assertion);
        }
    }
}
