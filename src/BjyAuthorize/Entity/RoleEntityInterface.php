<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Entity;

interface RoleEntityInterface
{
    /**
     * Get the id.
     *
     * @return string
     */
    public function getId();

    /**
     * Set the id.
     *
     * @param string $id
     * @return RoleInteface
     */
    public function setId($string);

    /**
     * Get the parent role
     *
     * @return Role
     */
    public function getParent();

    /**
     * Set the parent role.
     *
     * @param Role $role
     * @return RoleInterface
     */
    public function setParent($parent);
}