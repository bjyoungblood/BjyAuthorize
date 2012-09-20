<?php

namespace BjyAuthorize\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class HasRole extends AbstractPlugin
{
    protected $authorizeService;

    public function __invoke($role)
    {
        return in_array($role, $this->getAuthorizeService()->getRoles());
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