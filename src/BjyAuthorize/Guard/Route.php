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
use Zend\ServiceManager\ServiceLocatorInterface;

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

    /**
     * @param array                   $rules
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(array $rules, ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        foreach ($rules as $rule) {
            if (!is_array($rule['roles'])) {
                $rule['roles'] = array($rule['roles']);
            }

            $resource = 'route/' . $rule['route'];
            $this->rules[$resource] = array('roles' => $rule['roles']);

            if (isset($rule['assertion'])) {
                $this->rules[$resource]['assertion'] = $rule['assertion'];
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -1000);
    }

    /**
     * {@inheritDoc}
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
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
        $service    = $this->serviceLocator->get('BjyAuthorize\Service\Authorize');
        $match      = $event->getRouteMatch();
        $routeName  = $match->getMatchedRouteName();

        if ($service->isAllowed('route/' . $routeName)) {
            return;
        }

        $event->setError(static::ERROR);
        $event->setParam('route', $routeName);
        $event->setParam('identity', $service->getIdentity());
        $event->setParam('exception', new UnAuthorizedException('You are not authorized to access ' . $routeName));

        /* @var $app \Zend\Mvc\Application */
        $app = $event->getTarget();

        $app->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
    }
}
