<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\TableGateway\TableGateway;

/**
 * @author Simone Castellaneta <s.castel@gmail.com>
 * 
 * @return \Zend\Db\TableGateway\TableGateway
 */
class UserRoleServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * 
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TableGateway('user_role', $serviceLocator->get('bjyauthorize_zend_db_adapter'));
    }
}
