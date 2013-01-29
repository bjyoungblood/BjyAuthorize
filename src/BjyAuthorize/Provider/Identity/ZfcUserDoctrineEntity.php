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

/**
 * Identity provider which uses doctrine User and Role entities.
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 * @author Tom Oram <tom@scl.co.uk>
 */
class ZfcUserDoctrineEntity implements ProviderInterface
{
    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $defaultRole;

    /**
     * @param AuthenticationService $authService
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
            // get default/guest role
            return $this->getDefaultRole();
        }

        $roles = array();

        $user = $this->authService->getIdentity();

        if (!$user instanceof RoleProviderInterface) {
            return $this->getDefaultRole();
        }

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
