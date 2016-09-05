<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Role;

use BjyAuthorize\Acl\Role;
use Interop\Container\ContainerInterface;
use Zend\Db\Sql\Select;

/**
 * Role provider based on a {@see \Zend\Db\Adaper\Adapter}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class ZendDb implements ProviderInterface
{
    /**
     * @var ContainerInterface
     */
    protected $serviceLocator;

    /**
     * @var string
     */
    protected $tableName = 'user_role';

    /**
     * @var string
     */
    protected $identifierFieldName = 'id';

    /**
     * @var string
     */
    protected $roleIdFieldName = 'role_id';

    /**
     * @var string
     */
    protected $parentRoleFieldName = 'parent_id';

    /**
     * @param                         $options
     * @param ContainerInterface $serviceLocator
     */
    public function __construct($options, ContainerInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        if (isset($options['table'])) {
            $this->tableName = $options['table'];
        }

        if (isset($options['identifier_field_name'])) {
            $this->identifierFieldName = $options['identifier_field_name'];
        }

        if (isset($options['role_id_field'])) {
            $this->roleIdFieldName = $options['role_id_field'];
        }

        if (isset($options['parent_role_field'])) {
            $this->parentRoleFieldName = $options['parent_role_field'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        /* @var $tableGateway \Zend\Db\TableGateway\TableGateway */
        $tableGateway = $this->serviceLocator->get('BjyAuthorize\Service\RoleDbTableGateway');
        $sql = new Select();

        $sql->from($this->tableName);

        /* @var $roles Role[] */
        $roles = array();
        $indexedRows = array();
        $rowset = $tableGateway->selectWith($sql);

        // Pass 1: collect all rows and index them by PK
        foreach ($rowset as $row) {
            $indexedRows[$row[$this->identifierFieldName]] = $row;
        }

        // Pass 2: build a role for each indexed row
        foreach ($indexedRows as $row) {
            $parentRoleId = isset($row[$this->parentRoleFieldName])
                ? $indexedRows[$row[$this->parentRoleFieldName]][$this->roleIdFieldName] : null;
            $roleId = $row[$this->roleIdFieldName];
            $roles[$roleId] = new Role($roleId, $parentRoleId);
        }

        // Pass 3: Re-inject parent objects to preserve hierarchy
        foreach ($roles as $role) {
            $parentRoleObj = $role->getParent();

            if ($parentRoleObj && ($parentRoleId = $parentRoleObj->getRoleId())) {
                $role->setParent($roles[$parentRoleId]);
            }
        }

        return array_values($roles);
    }
}
