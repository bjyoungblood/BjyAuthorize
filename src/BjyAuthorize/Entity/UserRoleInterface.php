<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Entity;

/**
 * Interface for a use that is aware of Role entities.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface UserRoleInterface
{
    /**
     * Get role.
     *
     * @return \Zend\Permissions\Acl\Role\RoleInterface\RoleInterface[]
     */
    public function getRoles();
}