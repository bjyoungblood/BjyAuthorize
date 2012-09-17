<?php

namespace BjyAuthorize\Guard;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

interface GuardInterface extends ListenerAggregateInterface
{
    public function getResources();

    public function attach(EventManagerInterface $events);

    public function detach(EventManagerInterface $events);
}
