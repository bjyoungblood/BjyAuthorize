<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Role;

use BjyAuthorize\Acl\Role;
use Doctrine\ORM\EntityManager;

/**
 * Role provider based on a {@see \Doctrine\ORM\EntityManager}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 * @authro Tom Oram <tom@scl.co.uk>
 */
class DoctrineEntity implements ProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $roleEntityClass = 'BjyAuthorize\Entity\Role';

    /**
     * @param array         $options
     * @param EntityManager $entityManager
     */
    public function __construct(array $options, EntityManager $entityManager)
    {
        if (isset($options['role_entity_class'])) {
            $this->roleEntityClass = $options['role_entity_class'];
        }

        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        $result = $this->entityManager->getRepository($this->roleEntityClass)->findAll();
        // get roles associated with the logged in user
        $roles  = array();

        // Pass One: Build each object
        foreach ($result as $role) {
            $roleId = $role->getId();
            $parent = $role->getParent() ? $role->getParent()->getId() : null;
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
