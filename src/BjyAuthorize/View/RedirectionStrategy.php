<?php

/**
 * @author Rob Allen <rob@akrabat.com>
 */

namespace BjyAuthorize\View;

use BjyAuthorize\Service\Authorize;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ResponseInterface as Response;

class RedirectionStrategy implements ListenerAggregateInterface
{
    protected $redirectRoute = 'zfcuser/login';

    protected $redirectType = 'route';

    const REDIRECT_URL = 'url';
    const REDIRECT_ROUTE = 'route';

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    public function __construct($routeOrUrl = null, $redirectType = null)
    {
        if ($routeOrUrl) {
            $this->redirectRoute = $routeOrUrl;
        }

        if ($redirectType) {
            $this->redirectType = $redirectType;
        }
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), -5000);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onDispatchError(MvcEvent $e)
    {
        // Do nothing if the result is a response object
        $result = $e->getResult();
        if ($result instanceof Response) {
            return;
        }

        $router = $e->getRouter();
        $match  = $e->getRouteMatch();
        var_dump($e);

        if ($this->redirectType == RedirectionStrategy::REDIRECT_ROUTE) {
            // get url to the route
            $options['name'] = $this->redirectRoute;
            $url = $router->assemble(array(), $options);
        } else {
            $url = $this->redirectRoute;
        }

        // set up response to redirect to login page
        $response = $e->getResponse();
        if (!$response) {
            $response = new HttpResponse();
            $e->setResponse($response);
        }
        $response->getHeaders()->addHeaderLine('Location', $url . '?redirect=' . $redirect);
        $response->setStatusCode(302);
    }

    public function setRedirectRoute($route)
    {
        $this->redirectRoute = $route;
        return $this;
    }

    public function getRedirectRoute()
    {
        return $this->redirectRoute;
    }
}
