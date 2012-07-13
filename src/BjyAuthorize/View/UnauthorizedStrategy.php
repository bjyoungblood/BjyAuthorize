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

        // Common view variables
        $viewVariables = array(
           'error'      => $e->getParam('error'),
           'identity'   => $e->getParam('identity'),
        );

        $error = $e->getError();
        switch($error)
        {
            case 'error-unauthorized-controller':
                $viewVariables['controller'] = $e->getParam('controller');
                $viewVariables['action'] = $e->getParam('action');
                break;
            case 'error-unauthorized-route':
                $viewVariables['route'] = $e->getParam('route');
                break;
            default:
                // Do nothing if no error in the event
                return;
        }

        $model = new ViewModel($viewVariables);
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
