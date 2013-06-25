<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Collector;

use BjyAuthorize\Provider\Identity\ProviderInterface;
use Serializable;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\Role\RoleInterface;
use ZendDeveloperTools\Collector\CollectorInterface;

/**
 * Role collector - collects the role during dispatch
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RoleCollector implements CollectorInterface, Serializable
{
    const NAME     = 'bjy_authorize_role_collector';

    const PRIORITY = 150;

    /**
     * @var array|string[] collected role ids
     */
    protected $collectedRoles = array();

    /**
     * @var \BjyAuthorize\Provider\Identity\ProviderInterface|null
     */
    protected $identityProvider;

    /**
     * @param \BjyAuthorize\Provider\Identity\ProviderInterface $identityProvider
     */
    public function __construct(ProviderInterface $identityProvider)
    {
        $this->identityProvider = $identityProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        return static::PRIORITY;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(MvcEvent $mvcEvent)
    {
        if (! $this->identityProvider) {
            return;
        }

        $roles = $this->identityProvider->getIdentityRoles();

        if (! is_array($roles) && ! $roles instanceof \Traversable) {
            $roles = (array) $roles;
        }

        foreach ($roles as $role) {
            if ($role instanceof RoleInterface) {
                $role = $role->getRoleId();
            }

            if ($role) {
                $this->collectedRoles[] = (string) $role;
            }
        }
    }

    /**
     * @return array|string[]
     */
    public function getCollectedRoles()
    {
        return $this->collectedRoles;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize($this->collectedRoles);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $this->collectedRoles = unserialize($serialized);
    }
}
