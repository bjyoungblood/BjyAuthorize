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
 * Interface for a role entity
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface RoleWithParentInterface extends RoleInterface
{
    /**
     * Get the parent role
     *
     * @return Role
     */
    public function getParent();
}
