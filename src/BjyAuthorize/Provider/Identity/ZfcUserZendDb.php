<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Identity;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;
use ZfcUser\Service\User;

/**
 * Identity provider based on {@see \Zend\Db\Adapter\Adapter}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class ZfcUserZendDb implements ProviderInterface
{
    /**
     * @var User
     */
    protected $userService;

    /**
     * @var string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $defaultRole;

    /**
     * @var string
     */
    protected $tableName = 'user_role_linker';

    /**
     * @param Adapter $adapter
     * @param User    $userService
     */
    public function __construct(Adapter $adapter, User $userService)
    {
        $this->adapter     = $adapter;
        $this->userService = $userService;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        $authService = $this->userService->getAuthService();

        if (!$authService->hasIdentity()) {
            // get default/guest role
            return $this->getDefaultRole();
        } else {
            // get roles associated with the logged in user
            $sql  = new Sql($this->adapter);
            $select = $sql->select()->from($this->tableName);
            $where  = new Where();

            $where->equalTo('user_id', $authService->getIdentity()->getId());

            $results = $sql->prepareStatementForSqlObject($select->where($where))->execute();
            $roles     = array();

            foreach ($results as $i) {
                $roles[] = $i['role_id'];
            }

            return $roles;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     * {@inheritDoc}
     */
    public function setUserService($userService)
    {
        $this->userService = $userService;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultRole()
    {
        return $this->defaultRole;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultRole($defaultRole)
    {
        $this->defaultRole = $defaultRole;

        return $this;
    }
}
