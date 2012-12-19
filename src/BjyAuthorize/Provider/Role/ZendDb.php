<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Role;

use BjyAuthorize\Acl\Role;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Role provider based on a {@see \Zend\Db\Adaper\Adapter}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class ZendDb implements ProviderInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var string
     */
    protected $adapterName         = 'bjyauthorize_zend_db_adapter';

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
     * @param                         $options
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct($options, ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        if (isset($options['adapter'])) {
            $this->adapterName = $options['adapter'];
        }

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

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        /* @var $adapter \Zend\Db\Adapter\Adapter */
        $adapter      = $this->serviceLocator->get($this->adapterName);
        $tableGateway = new TableGateway($this->tableName, $adapter);
        $sql          = new Select;

        $sql->from($this->tableName);

        $rowset = $tableGateway->selectWith($sql);
        $roles  = array();

        // Pass One: Build each object
        foreach ($rowset as $row) {
            $roleId = $row[$this->roleIdFieldName];
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
