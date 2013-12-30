<?php

/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 * 
 */
namespace BjyAuthorize\Provider\Identity;

use BjyAuthorize\Exception\InvalidRoleException;
use Zend\Permissions\Acl\Role\RoleInterface;
use ZfcUser\Service\User;
use BjyAuthorize\Provider\Identity\ProviderInterface;

/**
 * Identity provider based on {@see \BjyAuthorize\Provider\Identity\ZfcUserZendDb}
 *
 * @author Mat Wright <mat@bstechnologies.com>
 */
class ZfcUserDoctrineMongoODM implements ProviderInterface
{

    /**
     *
     * @var User
     */
    protected $userService;

    /**
     *
     * @var string \Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $defaultRole;

    /**
     *
     * @var string
     */
    protected $collectionName = 'role';

    /**
     *
     * @param \ZfcUser\Service\User $userService            
     */
    public function __construct(User $userService)
    {
        $this->userService = $userService;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        $authService = $this->userService->getAuthService();
        
        if (! $authService->hasIdentity()) {
            return array(
                $this->getDefaultRole()
            );
        }
        
        // get role associated with the logged in user
        $userRoles = $authService->getIdentity()->getRoles();
        $roles = array();
        
        foreach ($userRoles as $role) {
            $roles[] = $role->getRoleId();
        }
        
        return $roles;
    }

    /**
     *
     * @return string \Zend\Permissions\Acl\Role\RoleInterface
     */
    public function getDefaultRole()
    {
        return $this->defaultRole;
    }

    /**
     *
     * @param string|\Zend\Permissions\Acl\Role\RoleInterface $defaultRole            
     *
     * @throws \BjyAuthorize\Exception\InvalidRoleException
     */
    public function setDefaultRole($defaultRole)
    {
        if (! ($defaultRole instanceof RoleInterface || is_string($defaultRole))) {
            throw InvalidRoleException::invalidRoleInstance($defaultRole);
        }
        
        $this->defaultRole = $defaultRole;
    }
}
