<?php

namespace BjyAuthorize\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Permissions\Acl\Acl;

class AuthorizeFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $config = $sl->get('Configuration');
        $config = $config['bjyauthorize'];

        $service = new Authorize($config, $sl);

        return $service;
    }
}
