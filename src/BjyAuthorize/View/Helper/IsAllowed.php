<?php

namespace BjyAuthorize\View\Helper;

use Zend\View\Helper\AbstractHelper;

class IsAllowed extends AbstractHelper
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
