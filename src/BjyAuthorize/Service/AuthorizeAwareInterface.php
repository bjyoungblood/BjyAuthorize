<?php

namespace BjyAuthorize\Service;

interface AuthorizeAwareInterface
{
    public function setAuthorizeService(Authorize $auth);
}
