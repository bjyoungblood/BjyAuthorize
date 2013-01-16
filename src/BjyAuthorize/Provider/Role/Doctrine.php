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
 */
class Doctrine implements ProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $tableName           = 'user_role';

    /**
     * @var string
     */
    protected $roleIdFieldName     = 'role_id';

    /**
     * @var string
     */
    protected $parentRoleFieldName = 'parent';

    /**
     * @param array         $options
     * @param EntityManager $entityManager
     */
    public function __construct(array $options, EntityManager $entityManager)
    {
        if (isset($options['table'])) {
            $this->tableName = $options['table'];
        }

        if (isset($options['role_id_field'])) {
            $this->roleIdFieldName = $options['role_id_field'];
        }

        if (isset($options['parent_role_field'])) {
            $this->parentRoleFieldName = $options['parent_role_field'];
        }

        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        // get roles associated with the logged in user
        $roles  = array();
        $rowset = $this
            ->entityManager
            ->getConnection()
            ->createQueryBuilder()
            ->select($this->roleIdFieldName, $this->parentRoleFieldName)
            ->from($this->tableName, $this->tableName)
            ->execute();

        // Pass One: Build each object
        foreach ($rowset as $row) {
            $roleId         = $row[$this->roleIdFieldName];
            $roles[$roleId] = new Role($roleId, $row[$this->parentRoleFieldName]);
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
