<?php

namespace BjyAuthorize\View\Helper;

use Zend\View\Helper\AbstractHelper;

class HasRole extends AbstractHelper
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