<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Exception;

/**
 * Invalid role exception for BjyAuthorize
 */
class InvalidRoleException extends InvalidArgumentException
{
    /**
     * @param mixed $role
     *
     * @return self
     */
    public static function invalidRoleInstance($role)
    {
        return new self(
            sprintf('Invalid role of type "%s" provided', is_object($role) ? get_class($role) : gettype($role))
        );
    }
}
