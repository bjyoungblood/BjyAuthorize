<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Resource;

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
    protected $adapterName         = 'bjyauthorize_zend_db_adapter';

    /**
     * @var string
     */
    protected $tableName           = 'acl_resource';

    /**
     * @var string
     */
    protected $resourceIdFieldName = 'id';

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

    if (isset($options['resource_id_field'])) {
        $this->resourceIdFieldName = $options['resource_id_field'];
    }
    }

    public function getResources()
    {
        /* @var $adapter \Zend\Db\Adapter\Adapter */
        $adapter      = $this->serviceLocator->get($this->adapterName);
        $tableGateway = new TableGateway($this->tableName, $adapter);
        $sql          = new Select;

        $sql->from($this->tableName);

        $rowset = $tableGateway->selectWith($sql);

        $resources = array();

        // Pass One: Build each object
        foreach ($rowset as $row) {
	       $resources[$row->{$this->resourceIdFieldName}] = array();
        }

       return $resources;
    }
}
