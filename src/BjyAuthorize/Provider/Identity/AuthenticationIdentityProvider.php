<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Identity;

use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Authentication\AuthenticationService;

/**
 * Simple identity provider to handle simply guest|user
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class AuthenticationIdentityProvider implements ProviderInterface
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
     * @var string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $authenticatedRole;

    /**
     * @param AuthenticationService          $authService
     */
    public function __construct(AuthenticationService $authService)
    {
        $this->authService   = $authService;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        if ($this->authService->getIdentity()) {
            $AuthorizedRole = $this->authenticatedRole instanceof RoleInterface ?
                $this->authenticatedRole->getRoleId() : $this->authenticatedRole;

            return array($AuthorizedRole);
        }

        $defaultRole = $this->defaultRole instanceof RoleInterface ?
            $this->defaultRole->getRoleId() : $this->defaultRole;

        return array($defaultRole);
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

    /**
     * Get the rule that's used if you're authenticated
     *
     * @return string
     */
    public function getAuthenticatedRole()
    {
        return $this->authenticatedRole;
    }

    /**
     * Set the role that's used if you're authenticated
     *
     * @param string $authenticatedRole
     */
    public function setAuthenticatedRole($authenticatedRole)
    {
        $this->authenticatedRole = $authenticatedRole;
    }
}
