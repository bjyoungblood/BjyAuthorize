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
use Zend\Cache\Storage\StorageInterface;

/**
 * Authorize service
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Authorize
{
    const TYPE_ALLOW = 'allow';

    const TYPE_DENY = 'deny';

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

    /**
     * @var \Closure|null
     */
    protected $loaded;

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param array                                         $config
     * @param \Zend\ServiceManager\ServiceLocatorInterface  $serviceLocator
     */
    public function __construct(array $config, ServiceLocatorInterface $serviceLocator)
    {
        $this->config         = $config;
        $this->serviceLocator = $serviceLocator;
        $that                 = $this;
        $this->loaded         = function () use ($that) {
            $that->load();
        };
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @param RoleProvider $provider
     *
     * @return self
     */
    public function addRoleProvider(RoleProvider $provider)
    {
        $this->loaded && $this->loaded->__invoke();

        $this->roleProviders[] = $provider;

        return $this;
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @param ResourceProvider $provider
     *
     * @return self
     */
    public function addResourceProvider(ResourceProvider $provider)
    {
        $this->loaded && $this->loaded->__invoke();

        $this->resourceProviders[] = $provider;

        return $this;
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @param RuleProvider $provider
     *
     * @return self
     */
    public function addRuleProvider(RuleProvider $provider)
    {
        $this->loaded && $this->loaded->__invoke();

        $this->ruleProviders[] = $provider;

        return $this;
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @param IdentityProvider $provider
     *
     * @return self
     */
    public function setIdentityProvider(IdentityProvider $provider)
    {
        $this->loaded && $this->loaded->__invoke();

        $this->identityProvider = $provider;

        return $this;
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @return IdentityProvider
     */
    public function getIdentityProvider()
    {
        $this->loaded && $this->loaded->__invoke();

        return $this->identityProvider;
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @param GuardInterface $guard
     *
     * @return self
     */
    public function addGuard(GuardInterface $guard)
    {
        $this->loaded && $this->loaded->__invoke();

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
     * @deprecated this method will be removed in BjyAuthorize 1.4.x+,
     *             please retrieve the guards from the `BjyAuthorize\Guards` service
     *
     * @return GuardInterface[]
     */
    public function getGuards()
    {
        $this->loaded && $this->loaded->__invoke();

        return $this->guards;
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 1.4.x+,
     *             please retrieve the identity from the
     *             `BjyAuthorize\Provider\Identity\ProviderInterface` service
     *
     * @return string
     */
    public function getIdentity()
    {
        $this->loaded && $this->loaded->__invoke();

        return 'bjyauthorize-identity';
    }

    /**
     * @return Acl
     */
    public function getAcl()
    {
        $this->loaded && $this->loaded->__invoke();

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
        $this->loaded && $this->loaded->__invoke();

        try {
            return $this->acl->isAllowed($this->getIdentity(), $resource, $privilege);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Initializes the service
     *
     * @internal
     *
     * @return void
     */
    public function load()
    {
        if (null === $this->loaded) {
            return;
        }

        $this->loaded = null;

        /** @var $cache StorageInterface */
        $cache      = $this->serviceLocator->get('BjyAuthorize\Cache');
        $success    = false;
        $this->acl  = $cache->getItem($this->config['cache_key'], $success);

        if (!($this->acl instanceof Acl) || !$success) {
            $this->loadAcl();
            $cache->setItem($this->config['cache_key'], $this->acl);
        }

        $this->setIdentityProvider($this->serviceLocator->get('BjyAuthorize\Provider\Identity\ProviderInterface'));

        $parentRoles = $this->getIdentityProvider()->getIdentityRoles();

        $this->acl->addRole($this->getIdentity(), $parentRoles);
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
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
                $this->addRoles(array($role->getParent()));
                $this->acl->addRole($role, $role->getParent());
            } elseif (!$this->acl->hasRole($role)) {
                $this->acl->addRole($role);
            }
        }
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
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
            } elseif (!$this->acl->hasResource($key)) {
                $this->acl->addResource($key, $parent);
            }
        }
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @param mixed $rule
     * @param mixed $type
     *
     * @throws \InvalidArgumentException
     */
    protected function loadRule(array $rule, $type)
    {
        $privileges = $assertion = null;
        $ruleSize   = count($rule);

        if (4 === $ruleSize) {
            list($roles, $resources, $privileges, $assertion) = $rule;
            $assertion = $this->serviceLocator->get($assertion);
        } elseif (3 === $ruleSize) {
            list($roles, $resources, $privileges) = $rule;
        } elseif (2 === $ruleSize) {
            list($roles, $resources) = $rule;
        } else {
            throw new \InvalidArgumentException('Invalid rule definition: ' . print_r($rule, true));
        }

        if (is_string($assertion)) {
            $assertion = $this->serviceLocator->get($assertion);
        }

        if (static::TYPE_ALLOW === $type) {
            $this->acl->allow($roles, $resources, $privileges, $assertion);
        } else {
            $this->acl->deny($roles, $resources, $privileges, $assertion);
        }
    }

    /**
     * Initialize the Acl
     */
    private function loadAcl()
    {
        $this->acl = new Acl();

        foreach ($this->serviceLocator->get('BjyAuthorize\RoleProviders') as $provider) {
            $this->addRoleProvider($provider);
        }

        foreach ($this->serviceLocator->get('BjyAuthorize\ResourceProviders') as $provider) {
            $this->addResourceProvider($provider);
        }

        foreach ($this->serviceLocator->get('BjyAuthorize\RuleProviders') as $provider) {
            $this->addRuleProvider($provider);
        }

        foreach ($this->serviceLocator->get('BjyAuthorize\Guards') as $guard) {
            $this->addGuard($guard);
        }

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
    }
}
