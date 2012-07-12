<?php

namespace BjyAuthorize\View;

use BjyAuthorize\Service\Authorize;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ViewModel;

class UnauthorizedStrategy implements ListenerAggregateInterface
{
    /**
     * @var string
     */
    protected $template = 'error/403';

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'prepareViewModel'), -5000);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function prepareViewModel(MvcEvent $e)
    {
        // Do nothing if the result is a response object
        $result = $e->getResult();
        if ($result instanceof Response) {
            return;
        }

        $error = $e->getError();
        switch($error)
        {
            case 'error-unauthorized-controller':
                $model = new ViewModel(array(
                    'error'      => $e->getParam('error'),
                    'controller' => $e->getParam('controller'),
                    'action'     => $e->getParam('action'),
                    'identity'   => $e->getParam('identity'),
                ));
                break;
            case 'error-unauthorized-route':
                $model = new ViewModel(array(
                    'error'      => $e->getParam('error'),
                    'route'      => $e->getParam('route'),
                    'identity'   => $e->getParam('identity'),
                ));
                break;
            default:
                // Do nothing if no error in the event
                return;
        }
        $model->setTemplate($this->getTemplate());
        $e->getViewModel()->addChild($model);

        $response = $e->getResponse();
        if (!$response) {
            $response = new HttpResponse();
            $e->setResponse($response);
        }
        $response->setStatusCode(403);
    }
}
