<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Entity;

use Zend\Permissions\Acl\Role\RoleInterface;

class Role implements RoleInterface, RoleEntityInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var Role
     */
    protected $parent;

    /**
     * Get the id.
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->id;
    }

    /**
     * Get the id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id.
     *
     * @param string $id
     * @return RoleInteface
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     * @return RoleEntityInterface
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }
}