<?php

namespace BjyAuthorize\Provider\Role;

use BjyAuthorize\Acl\Role;
use Doctrine\ORM\EntityManager;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class Doctrine implements ProviderInterface, ServiceManagerAwareInterface
{
    protected $em;
    protected $sm;

    protected $tableName           = 'user_role';
    protected $roleIdFieldName     = 'role_id';
    protected $parentRoleFieldName = 'parent';

    public function setOptions($options)
    {
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');

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

        $result = $builder->execute();

        $roles = array();
        foreach($result as $row) {
            $roles[] = new Role($row[$this->roleIdFieldName], $row[$this->parentRoleFieldName]);
        }
        return $roles;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
    }
}
