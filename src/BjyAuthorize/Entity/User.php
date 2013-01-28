<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use ZfcUser\Entity\User as ZfcUserEntity;

class User extends ZfcUserEntity implements UserRoleInterface
{
    /**
     * @var ArrayCollection
     */
    protected $roles;

    /**
     * Initialies the roles variable.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * Get role.
     *
     * @return ArrayCollection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add a role to the user.
     *
     * @param Role $role
     * @return UserInterface
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
        return $this;
    }
}