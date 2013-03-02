<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Acl;

use BjyAuthorize\Exception;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Base role object
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Role implements RoleInterface, HierarchicalRoleInterface
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
        if (null !== $roleId) {
            $this->setRoleId($roleId);
        }
        if (null !== $parent) {
            $this->setParent($parent);
        }
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
        $this->roleId = (string) $roleId;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param RoleInterface|string|null $parent
     *
     * @throws \BjyAuthorize\Exception\InvalidRoleException
     *
     * @return self
     */
    public function setParent($parent)
    {
        if (null === $parent) {
            $this->parent = null;

            return $this;
        }

        if (is_string($parent)) {
            $this->parent = new Role($parent);

            return $this;
        }

        if ($parent instanceof RoleInterface) {
            $this->parent = $parent;

            return $this;
        }

        throw Exception\InvalidRoleException::invalidRoleInstance($parent);
    }
}
