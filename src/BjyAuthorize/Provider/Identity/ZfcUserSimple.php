<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Identity;

use ZfcUser\Service\User;

/**
 * Simple identity provider to handle simply guest|user
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class ZfcUserSimple implements ProviderInterface
{
    /**
     * @var User
     */
    protected $userService;

    /**
     * @var string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $defaultRole;

    /**
     * @var string
     */
    protected $defaultAuthorizedRole;

    /**
     * @param User          $userService
     */
    public function __construct(User $userService)
    {
        $this->userService   = $userService;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        if ($this->userService->getAuthService()->getIdentity()) {

            return array($this->defaultAuthorizedRole);
        }

        return array($this->defaultRole);
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
     * Get the rule that's used if you're authorized
     *
     * @return string
     */
    public function getDefaultAuthorizedRole()
    {
        return $this->defaultAuthorizedRole;
    }

    /**
     * Set the role that's used if you're authorized
     *
     * @param string $defaultAuthorizedRole
     */
    public function setDefaultAuthorizedRole($defaultAuthorizedRole)
    {
        $this->defaultAuthorizedRole = $defaultAuthorizedRole;
    }
}
