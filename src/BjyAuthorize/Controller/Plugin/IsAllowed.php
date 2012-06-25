<?php

namespace BjyAuthorize\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class IsAllowed extends AbstractPlugin
{
    protected $authorizeService;

    public function __invoke($resource, $privilege = null)
    {
        return $this->getAuthorizeService()->isAllowed($resource, $privilege);
    }

    public function getAuthorizeService()
    {
        return $this->authorizeService;
    }

    public function setAuthorizeService($authorize)
    {
        $this->authorizeService = $authorize;
        return $this;
    }
}
