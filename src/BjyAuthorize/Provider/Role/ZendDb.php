<?php

namespace BjyAuthorize\Provider\Role;

use BjyAuthorize\Acl\Role;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class ZendDb implements ProviderInterface
{
    protected $sm;

    protected $tableName           = 'user_role';
    protected $roleIdFieldName     = 'role_id';
    protected $parentRoleFieldName = 'parent';

    public function __construct($options, $serviceManager)
    {
        $this->sm = $serviceManager;

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
        $tableGateway = new TableGateway($this->tableName, $this->sm->get('Zend\Db\Adapter\Adapter'));

        $sql = new Select;
        $sql->from($this->tableName);

        $rowset = $tableGateway->selectWith($sql);

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
