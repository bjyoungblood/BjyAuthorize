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
use ZfcUser\Entity\UserInterface;

/**
 * Identity provider for DoctrineMongoODM
 * 
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
     * @param \ZfcUser\Service\User $userService            
     */
    public function __construct(User $userService, RoleInterface $defaultRole)
    {
        $this->userService = $userService;
        $this->defaultRole = $defaultRole;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        $authService = $this->userService->getAuthService();
        
        //if user is not logged in or identity is not a valid User object return default role
        if (! $authService->getIdentity() || !$authService->getIdentity() instanceof UserInterface) {
            return array(
                $this->defaultRole
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


 
}
