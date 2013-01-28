<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Entity;

use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * 
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class Role implements RoleInterface, RoleEntityInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $roleId;

    /**
     * @var Role
     */
    protected $parent;

    /**
     * Get the id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id.
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * Get the role id.
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Set the role id.
     *
     * @param string $roleId
     */
    public function setRoleId($roleId)
    {
        $this->roleId = (string)$roleId;
    }

    /**
     * Get the parent role
     *
     * @return Role
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the parent role.
     *
     * @param Role $role
     */
    public function setParent(Role $parent)
    {
        $this->parent = $parent;
    }
}