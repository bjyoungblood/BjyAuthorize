<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Guard;

use BjyAuthorize\Exception\UnAuthorizedException;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * Route Guard listener, allows checking of permissions
 * during {@see \Zend\Mvc\MvcEvent::EVENT_ROUTE}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Route extends AbstractGuard
{
    /**
     * Marker for invalid route errors
     */
    const ERROR = 'error-unauthorized-route';

    protected function extractResourcesFromRule(array $rule)
    {
        return array('route/' . $rule['route']);
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -1000);
    }

    /**
     * Event callback to be triggered on dispatch, causes application error triggering
     * in case of failed authorization check
     *
     * @param MvcEvent $event
     *
     * @return void
     */
    public function onRoute(MvcEvent $event)
    {
        /* @var $service \BjyAuthorize\Service\Authorize */
        $service = $this->serviceLocator->get('BjyAuthorize\Service\Authorize');
        $match = $event->getRouteMatch();
        $routeName = $match->getMatchedRouteName();

        if ($service->isAllowed('route/' . $routeName)) {
            return;
        }

        $event->setError(static::ERROR);
        $event->setParam('route', $routeName);
        $event->setParam('identity', $service->getIdentity());
        $event->setParam('exception', new UnAuthorizedException('You are not authorized to access ' . $routeName));

        /* @var $app \Zend\Mvc\Application */
        $app = $event->getTarget();
        $eventManager = $app->getEventManager();
        $eventManager->setEventPrototype($event);
        $eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR, null, $event->getParams());
    }
}
