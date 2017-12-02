<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class FrontController implements \App\FrontControllerInterface
{
    protected $uri;
    protected $container;
    protected $controllerClass;
    protected $action;
    protected $routes;
    protected $request;
    protected $matcher;


    public function __construct($request, $routes, $container)
    {
        $this->container = $container;
        $this->request = $request;
        $this->routes = $routes;
        $this->uri = $this->request->getPathInfo();

        $context = new RequestContext();
        $context->fromRequest($this->request);
        $this->matcher = new UrlMatcher($this->routes, $context);
    }

    public function setControllerAction($parameters)
    {
        if (!is_null($parameters)) {
            foreach ($parameters as $key => $value) {
                $this->request->attributes->set($key, $value);
            }

            $controllerMap = preg_split('/::/', $parameters['_controller']);
            $this->controllerClass = $controllerMap[0];
            $this->action = isset($controllerMap[1]) ? $controllerMap[1] : null;
        } else {
            throw new \Exception('No arguments found.');
        }
    }

    public function callAction($method)
    {
        if (class_exists($this->controllerClass) && $method) {
            $controller = new $this->controllerClass($this->container);
            if (method_exists($controller, $method)) {
                $response = $controller->$method($this->request);
                return $response;
            } else {
                throw new \Exception('Action not found.');
            }
        } else {
            throw new \Exception('Controller not found.');
        }
    }

    public function run()
    {
        try {
            $parameters = $this->matcher->match($this->uri);
            $this->setControllerAction($parameters);
            $response = $this->callAction($this->action);
        } catch (\Routing\Exception\ResourceNotFoundException $e) {
            $response = new Response('Not found', 404);
        }

        $response->send();
    }
}
