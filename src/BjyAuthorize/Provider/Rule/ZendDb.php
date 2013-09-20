<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Rule;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorInterface;

class ZendDb implements ProviderInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var string
     */
    protected $adapterName      = 'bjyauthorize_zend_db_adapter';

    /**
     * @var string
     */
    protected $tableName        = 'acl_rule';

    /**
     * @var string
     */
    protected $ruleType         = 'type';

    /**
     * @var string
     */
    protected $roleIdField      = 'roleId';

    /**
     * @var string
     */
    protected $resourceIdField  = 'resourceId';

    /**
     * @var string
     */
    protected $privilegeField   = 'privilege';

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

        if (isset($options['rule_type'])) {
            $this->ruleType = $options['rule_type'];
        }

        if (isset($options['role_id_field'])) {
            $this->roleIdField = $options['role_id_field'];
        }

        if (isset($options['resource_id_field'])) {
            $this->resourceIdField = $options['resource_id_field'];
        }

        if (isset($options['privilege_id_field'])) {
            $this->privilegeField = $options['privilege_id_field'];
        }
    }

    public function getRules()
    {
        /* @var $adapter \Zend\Db\Adapter\Adapter */
        $adapter      = $this->serviceLocator->get($this->adapterName);
        $tableGateway = new TableGateway($this->tableName, $adapter);
        $sql          = new Select;

        $sql->from($this->tableName);

        $rowset = $tableGateway->selectWith($sql);

        $rules = array();

        // Pass One: Build each object
        foreach ($rowset as $row) {
            $found = false;
            foreach ($rules as $ruleType => &$ruleTypeArray) {
                foreach ($ruleTypeArray as $key => &$rule) {
                    if ($row->{$this->resourceIdField} === $rule[1] && $row->{$this->privilegeField} === $rule[2]) {
                        $rule[0][] = $row->{$this->roleIdField};
                        $found      = true;
                    }
                }
            }
            if (!$found) {
                $rules[$row->{$this->ruleType}][] = array(
                    array($row->{$this->roleIdField}),
                    $row->{$this->resourceIdField},
                    $row->{$this->privilegeField}
                );
            }
        }
        return $rules;
    }
}
