<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Role;

use BjyAuthorize\Acl\Role;
use BjyAuthorize\Acl\RoleInterface;
use BjyAuthorize\Acl\HierarchicalRoleInterface;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Role provider based on a {@see \Doctrine\ORM\EntityManager}
 *
 * @authro Tom Oram <tom@scl.co.uk>
 */
class DoctrineEntity implements ProviderInterface
{
    /**
     * @var ObjectRepository
     */
    protected $objectRepository;

    /**
     * @param ObjectRepositoy $objectRepository
     */
    public function __construct(ObjectRepository $objectRepository)
    {
        $this->objectRepository = $objectRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        $result = $this->objectRepository->findAll();

        // get roles associated with the logged in user
        $roles  = array();

        // Pass One: Build each object
        foreach ($result as $role) {
            if ($role instanceof RoleInterface) {
                continue;
            }

            $roleId = $role->getRoleId();

            $parent = null;

            if ($role instanceof HierarchicalRoleInterface) {
                if ($role->getParent()) {
                    $parent = $role->getParent()->getRoleId();
                }
            }

            $roles[$roleId] = new Role($roleId, $parent);
        }

        // Pass Two: Re-inject parent objects to preserve hierarchy
        /* @var $roleObj Role */
        foreach ($roles as $roleObj) {
            $parentRoleObj = $roleObj->getParent();

            if ($parentRoleObj && $parentRoleObj->getRoleId()) {
                $roleObj->setParent($roles[$parentRoleObj->getRoleId()]);
            }
        }

        return array_values($roles);
    }
}
