<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Acl;

use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Base role object
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Role implements RoleInterface
{
    /**
     * @var string
     */
    protected $roleId;

    /**
     * @var RoleInterface
     */
    protected $parent;

    /**
     * @param string|null               $roleId
     * @param RoleInterface|string|null $parent
     */
    public function __construct($roleId = null, $parent = null)
    {
        if (isset($parent) && !($parent instanceof RoleInterface)) {
            $parent = new Role($parent);
        }

        $this->roleId   = $roleId;
        $this->parent   = $parent;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * @param string $roleId
     *
     * @return self
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;

        return $this;
    }

    /**
     * @return RoleInterface|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param RoleInterface|string|null $parent
     *
     * @return self
     */
    public function setParent($parent)
    {
        if (isset($parent) && !($parent instanceof RoleInterface)) {
            $parent = new Role($parent);
        }

        $this->parent = $parent;

        return $this;
    }
}
