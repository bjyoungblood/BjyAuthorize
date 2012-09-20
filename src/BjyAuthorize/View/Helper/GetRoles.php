<?php

namespace BjyAuthorize\View\Helper;

use Zend\View\Helper\AbstractHelper;

class GetRoles extends AbstractHelper
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