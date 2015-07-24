<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Guard;

use BjyAuthorize\Exception\UnAuthorizedException;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Exception;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Tree Route Guard listener, allows checking of permissions
 * during {@see \Zend\Mvc\MvcEvent::EVENT_ROUTE}
 *
 * @author Marvin Feldmann <breyndot.echse@gmail.com>
 */
class TreeRoute extends Route
{
    /**
     * {@inheritDoc}
     */
    public function onRoute(MvcEvent $event)
    {
        $service        = $this->serviceLocator->get('BjyAuthorize\Service\Authorize');
        $match          = $event->getRouteMatch();
        $routeName      = $match->getMatchedRouteName();
        $routeNameBase  = $routeName;
        $lastSlashPos   = strlen($routeName);

        do {
            $routeNameBase = substr($routeNameBase, 0,  $lastSlashPos);
            if ($service->isAllowed('route/' . $routeNameBase)) {
                return;
            }
            if (isset($this->rules['route/' . $routeNameBase])) {
                break;
            }
        } while($lastSlashPos = strrpos($routeNameBase, '/'));

        $event->setError(static::ERROR);
        $event->setParam('route', $routeName);
        $event->setParam('identity', $service->getIdentity());
        $event->setParam('exception', new UnAuthorizedException('You are not authorized to access ' . $routeName));

        /* @var $app \Zend\Mvc\Application */
        $app = $event->getTarget();

        $app->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
    }

}
