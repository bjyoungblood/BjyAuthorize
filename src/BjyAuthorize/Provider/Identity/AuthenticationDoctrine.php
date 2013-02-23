<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Identity;

use BjyAuthorize\Provider\Role\ProviderInterface as RoleProviderInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Identity provider which uses doctrine User and Role entities.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class AuthenticationDoctrine implements ProviderInterface
{
    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    protected $authService;

    /**
     * @var string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $defaultRole;

    /**
     * @param \Zend\Authentication\AuthenticationService $authService
     */
    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        if (!$this->authService->hasIdentity()) {
            return array($this->getDefaultRole());
        }

        $user = $this->authService->getIdentity();

        if (!($user instanceof RoleInterface || $user instanceof RoleProviderInterface)) {
            return array($this->getDefaultRole());
        }

        if ($user instanceof RoleInterface) {
            return array($user->getRoleId());
        }

        /* @var $user \BjyAuthorize\Provider\Role\ProviderInterface */

        $roles = array();

        foreach ($user->getRoles() as $role) {
            $roles[] = $role->getRoleId();
        }

        return $roles;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultRole()
    {
        return $this->defaultRole;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultRole($defaultRole)
    {
        $this->defaultRole = $defaultRole;
    }
}
