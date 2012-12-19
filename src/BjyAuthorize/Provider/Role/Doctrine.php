<?php

namespace BjyAuthorize\Provider\Role;

use BjyAuthorize\Acl\Role;
use Doctrine\ORM\EntityManager;

class Doctrine implements ProviderInterface
{
    protected $em;

    protected $tableName           = 'user_role';
    protected $roleIdFieldName     = 'role_id';
    protected $parentRoleFieldName = 'parent';

    public function __construct($options, $serviceManager)
    {
        $this->em = $serviceManager->get('doctrine.entitymanager.orm_default');

        if (isset($options['table'])) {
            $this->tableName = $options['table'];
        }

        if (isset($options['role_id_field'])) {
            $this->roleIdFieldName = $options['role_id_field'];
        }

        if (isset($options['parent_role_field'])) {
            $this->parentRoleFieldName = $options['parent_role_field'];
        }
    }

    public function getRoles()
    {
        // get roles associated with the logged in user
        $builder = new \Doctrine\DBAL\Query\QueryBuilder($this->em->getConnection());
        $builder->select($this->roleIdFieldName,$this->parentRoleFieldName)
            ->from($this->tableName, $this->tableName);

        $rowset = $builder->execute();

        $roles = array();
        // Pass One: Build each object
        foreach ($rowset as $row) {
            $roleId = $row[$this->roleIdFieldName];
            $roles[$roleId] = new Role($roleId, $row[$this->parentRoleFieldName]);
        }
        // Pass Two: Re-inject parent objects to preserve hierarchy
        foreach ($roles as $roleId=>$roleObj) {
            $parentRoleObj = $roleObj->getParent();
            if ($parentRoleObj && $parentRoleObj->getRoleId()) {
                $roleObj->setParent($roles[$parentRoleObj->getRoleId()]);
            }
        }
        return array_values($roles);
    }
}
