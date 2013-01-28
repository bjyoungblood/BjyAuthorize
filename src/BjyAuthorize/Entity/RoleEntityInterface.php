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
     * Returns the string identifier of the Role
     *
     * @return string
     * @todo Should this be in here or just leave it in Acl Role Interface?
     */
    public function getRoleId();

    /**
     * Get the parent role
     *
     * @return Role
     */
    public function getParent();
}