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

    public function __construct($options, $serviceManager)
    {
        $this->sm = $serviceManager;
    }

    public function getRoles()
    {
        $tableGateway = new TableGateway('user_role', $this->sm->get('Zend\Db\Adapter\Adapter'));

        $sql = new Select;
        $sql->from('user_role');

        $rowset = $tableGateway->selectWith($sql);

        $roles = array();
        foreach ($rowset as $row) {
            $roles[] = new Role($row['role_id'], $row['parent']);
        }

        return $roles;
    }
}
