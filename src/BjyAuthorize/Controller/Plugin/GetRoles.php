<?php

namespace BjyAuthorize\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class GetRoles extends AbstractPlugin
{
    protected $authorizeService;

    public function __invoke()
    {
        return $this->getAuthorizeService()->getRoles();
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